<?php

namespace App\Controller;

use DateTime;
use App\Entity\Fil;
use App\Entity\Vis;
use App\Entity\Game;
use App\Entity\Bombe;
use App\Entity\Etape;
use App\Entity\Trigg;
use App\Entity\Commut;
use App\Form\GameType;
use App\Entity\Scenario;
use App\Form\GameEditType;
use App\Entity\ObjetScenario;
use App\Repository\GameRepository;
use App\Repository\TriggRepository;
use App\Controller\OutilsController;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExamAdminController extends OutilsController
{
    //-----------------------------------------------------------------------------------
    //Fonctions avec route ==============================================================
    //-----------------------------------------------------------------------------------
    /**
     * @Route("/escape/admin", name="escape_admin")
     * Affiche la page d'accueil de l'espace admin
     */
    public function index(GameRepository $repo, EntityManagerInterface $manager)
    {
        //Initialisation des scénario si bdd vide (laisser en commentaire)
        //$this->CreationScenarioExam($manager);

        $games = $repo->findAll();
        return $this->render('escape_admin/index.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/escape/admin/newgame", name="escape_admin_newgame")
     * Affiche la page de création d'une nouvelle partie
     */
    public function creerGame(Request $request, EntityManagerInterface $manager):Response
    {
        $variables['request'] = $request;
        $variables['manager'] = $manager;
        $variables['element'] = new Game();
        $variables['classType'] = GameType::class;
        $variables['pagedebase'] = 'escape_admin/newgame.html.twig';
        $variables['pagederesultat'] = 'escape_admin';
        $variables['titre'] = "Création d'une partie";
        $variables['texteConfirmation'] = "La partie ### a bien été créé !";
        $options['dependances'] = array('Joueurs' => 'Game', 'Commuts' => 'Game');        
        $options['texteConfirmationEval'] = ["###" => '$element->getId();'];
        $options['actions'] = array(['name' => 'action_initScenario', 'params' => []]);
        return $this->afficherFormulaire($variables, $options);
    }

    /**
     * @Route("/escape/admin/edit/{id}", name="escape_admin_edit")
     * Affiche la page de création d'une nouvelle partie
     */
    public function editGame(GameRepository $repo, $id, JoueurRepository $repoJoueur, Request $request, EntityManagerInterface $manager):Response
    {
        $game = $repo->find($id);

        $variables['request'] = $request;
        $variables['manager'] = $manager;
        $variables['element'] = $game;
        $variables['classType'] = GameEditType::class;
        $variables['pagedebase'] = 'escape_admin/editgame.html.twig';
        $variables['pagederesultat'] = 'escape_admin';
        $variables['titre'] = "Edition d'une partie";
        $variables['texteConfirmation'] = "La partie ### a bien été éditée !";
        $options['dependances'] = array('Joueurs' => 'Game');  
        $options['deletes'] = array(['findBy' => 'game', 'classEnfant' => 'joueurs', 'repo' => $repoJoueur]);  
        $options['texteConfirmationEval'] = ["###" => '$element->getId();'];
        $options['actions'] = ['name' => 'action_editScenario', 'params' => []];
        return $this->afficherFormulaire($variables, $options);
    }

    //-----------------------------------------------------------------------------------
    // ACTIONS A UTILIERS LORS DE LA VALIDATION D'UN FORMULAIRE =========================
    //-----------------------------------------------------------------------------------
    /**
     * ACTION : Base pour l'initialisation d'un scénario dans une partie
     */
    protected function action_initScenario(Object $game, $params, $request)
    {
        //Choisi l'init en fontion du scénario
        switch($game->getScenario()->getTitre())
        {
            case "L'exam !":
                $game = $this->action_initExam($game, $params);
            break;
        }
        //Assignation des étapes dans la partie
        foreach($game->getScenario()->getEtapes() as $etape)
        {
            $commut = new Commut();
            //$commut->setGame($game);
            $commut->setEtape($etape);
            $commut->setEtat(false);
            $commut->setDeblocable(false); // A modifier
            $game->addCommut($commut);
        }
        return $game;
    }
    
    /**
     * ACTION : EXAM - Crée une bombe avec fils et vis lors de la création d'une partie de l'exam
     */
     protected function action_initExam(Object $game, $params)
    {
        //Création d'un objet de scénario
        $objetBombe = new ObjetScenario();
        //Création de la bombe
        $objetBombe->setBombe($this->_CreateBombe());
        //Ajout de l'objetScenario dans la partie
        $game->addObjetsScenario($objetBombe);

        return $game;
    }

    /**
     * ACTION : Choisi l'action lors d'un edit en fonction du scénario
     */
    protected function action_editScenario(Object $game, $params, $request)
    {
        switch($game->getScenario()->getTitre())
        {
            case "L'exam !":
                $game = $this->action_editExam($game, $params, $request);
            break;
        }
        return $game;
    }
    
    /**
     * ACTION : EXAM - Action lors de l'edit du scénario l'exam
     */
     protected function editExam(Object $game, $params, $request)
    {
        foreach($game->getObjetsScenario() as $objet)
        {
            //Permet d'éditer la durée de la bombe
            if($objet->getBombe() != null)
            {
                $duree = $request->request->get('game_edit')['duree'];
                if($duree != null)
                {
                    $objet->getBombe()->setDuration($duree*60);
                }
            }
        }
        return $game;
    }
    //-----------------------------------------------------------------------------------
    // FONCTIONS DE SIMPLIFICATION EN PRIVATE ===========================================
    //-----------------------------------------------------------------------------------
    /**
     * Création d'une étape obligatoirment sur false en début de partie
     */
    private function CreationEtape($etapes, $titre, $descriptif, $parents = null, $automatique = false)
    {
        $etape = new Etape();        
        $etape->setTitre($titre);
        $etape->setDescriptif($descriptif);
        $etape->setAutomatique($automatique);
        if($parents != null)
        {
            foreach($parents as $parent)
            {
                $etape->addParent($etapes[$parent]);
            }
        }
        $etapes[$titre] = $etape;
        return $etapes;        
    }

    //-----------------------------------------------------------------------------------
    // DECOUPE DE FONTIONS POUR LISIBILITE ==============================================
    //-----------------------------------------------------------------------------------
    /**
     * EXAM - Création de la bombe pour le scénario
     *
     * @return Bombe
     */
    private function _CreateBombe()
    {
        $bombe = new Bombe();
        //Création de 6 fils
        $couleurs = array('rouge', 'blanc', 'bleu', 'jaune', 'noir');
        for($i=1; $i<=6; $i++)
        {
            $fil = new Fil();
            $fil->setCouleur($couleurs[array_rand($couleurs, 1)]);
            $fil->setEtat(true);
            $bombe->addFil($fil);
        }
        //Création des 4 vis
        for($i=1; $i<=4; $i++)
        {
            $vis = new Vis();
            $vis->setEtat(true);
            $bombe->addVi($vis);
        }
        //Durée par défaut à 1h30
        $bombe->setDuration(90*60);
        //La pince n'est pas disponible
        $bombe->setPince(false);
        //On retourne la bombe créée
        return $bombe;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // CREATION DE BASE DES SCENARIO
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * EXAM - Création du scénario
     */
    private function CreationScenarioExam($manager)
    {
        //Création du scnenario
        $scenario = new Scenario();
        $scenario->setTitre("L'exam !");
        $etapes = Array();

        //Création des différents étapes automatiques du scenario
        $etapes = $this->CreationEtape($etapes, "StartGame", "La partie est débuté", null, true);
         $etapes = $this->CreationEtape($etapes, "VisionageIntro", "Visualisation de la vidéo d'introduction", ['StartGame'], true);
          $etapes = $this->CreationEtape($etapes, "StartBombe", "Mise en route de la bombe", ['VisionageIntro'], true);
           $etapes = $this->CreationEtape($etapes, "PinceActive", "La pince est activé sur la bombe", ['StartBombe'], true);
           $etapes = $this->CreationEtape($etapes, "VisSupprimees", "Les 4 vis sont retirées", ['StartBombe'], true);
            $etapes = $this->CreationEtape($etapes, "DesamorcageRate", "Vous avez coupé le mauvais fil...", ['PinceActive', 'VisSupprimees'], true);
            $etapes = $this->CreationEtape($etapes, "DesamorcageReussi", "Vous avez coupé le mauvais fil...", ['PinceActive', 'VisSupprimees'], true);
           $etapes = $this->CreationEtape($etapes, "Boum", "Trop tard, la bombe a explosée", ['StartBombe'], true);
           $etapes = $this->CreationEtape($etapes, "FinGame", "La partie est fini", ['StartBombe'], true);
        
        
        //Accessible dès le début
        $etapes = $this->CreationEtape($etapes, "obj_cle_Sas", "Clé du sas (sous la table)", ['StartGame']);
            $etapes = $this->CreationEtape($etapes, "bte_Sas", "...", ['obj_cle_Sas']);
                $etapes = $this->CreationEtape($etapes, "obj_BoitesArchives", "3 dans la salle et 3 dans le sas", ['bte_Sas']);
            
        $etapes = $this->CreationEtape($etapes, "obj_cle_CadenasVert", "Clé du sac umbrella (en hauteur)", ['StartGame']);
            $etapes = $this->CreationEtape($etapes, "bte_SacUmbrella", "Dans la plante", ['obj_cle_CadenasVert']);
                $etapes = $this->CreationEtape($etapes, "ind_BoitesArchives", "Dans le sac umbrella", ['bte_SacUmbrella']);
                $etapes = $this->CreationEtape($etapes, "obj_Batonnets", "Dans le sac umbrella", ['bte_SacUmbrella']);

                    $etapes = $this->CreationEtape($etapes, "egm_Archives", "Code bleu 251 pour le sac à dos", ['ind_BoitesArchives', 'obj_BoitesArchives']);
                        $etapes = $this->CreationEtape($etapes, "bte_SacADos", "...", ['egm_Archives']);
                            $etapes = $this->CreationEtape($etapes, "obj_Agenda", "???", ['bte_SacADos']);
                                $etapes = $this->CreationEtape($etapes, "obj_CarteFrance", "...", ['obj_Agenda']);
                                    $etapes = $this->CreationEtape($etapes, "egm_CarteFrance", "Code 963 pour la boite ptouch", ['obj_CarteFrance', 'obj_Agenda']);
                                        $etapes = $this->CreationEtape($etapes, "bte_Ptouch", "...", ['bte_Sas', 'egm_CarteFrance']);
                                            $etapes = $this->CreationEtape($etapes, "obj_Cds", "5 cds, pochettes et boitiers", ['bte_Ptouch']);
                                            $etapes = $this->CreationEtape($etapes, "ind_Cds", "...", ['bte_Ptouch']);
                                                $etapes = $this->CreationEtape($etapes, "egm_Cds", "Code 138 pour la boite micro", ['ind_Cds', 'obj_Cds']);
                                                    $etapes = $this->CreationEtape($etapes, "bte_Micro", "...", ['bte_Sas', 'egm_Cds']);
                                                        $etapes = $this->CreationEtape($etapes, "obj_Souris", "...", ['bte_Micro']);
                            $etapes = $this->CreationEtape($etapes, "ind_Trombones", "???", ['bte_SacADos']);
                                $etapes = $this->CreationEtape($etapes, "obj_Trombones", "Sur le bureau", ['ind_Trombones']);
                                    $etapes = $this->CreationEtape($etapes, "egm_Trombones", "Code 534 pour la boite à code rouge", ['ind_Trombones', 'obj_Trombones']);
                                        $etapes = $this->CreationEtape($etapes, "bte_BoiteCodeRouge", "...", ['bte_SacADos', 'egm_Trombones']);
                                        $etapes = $this->CreationEtape($etapes, "obj_Pile", "...", ['bte_BoiteCodeRouge']);
                                        $etapes = $this->CreationEtape($etapes, "obj_PiecesPuzzles", "...", ['bte_BoiteCodeRouge']);

        $etapes = $this->CreationEtape($etapes, "obj_cle_CadenasRouge", "Clé du boituer de CD (dans le tube de colle)", ['StartGame']);
                                            $etapes = $this->CreationEtape($etapes, "bte_BoitierCD", "...", ['bte_Ptouch', 'obj_cle_CadenasRouge']);
                                                $etapes = $this->CreationEtape($etapes, "obj_ManuelDemineurB", "...", ['bte_BoitierCD']);
                                                $etapes = $this->CreationEtape($etapes, "obj_PileFace", "...", ['bte_BoitierCD']);
                                                $etapes = $this->CreationEtape($etapes, "obj_cle_CaissetteJaune", "...", ['bte_BoitierCD']);
                                                    $etapes = $this->CreationEtape($etapes, "bte_CaissetteJaune", "...", ['obj_cle_CaissetteJaune']);
                                                        $etapes = $this->CreationEtape($etapes, "obj_Disquettes", "...", ['bte_CaissetteJaune']);
                                                        $etapes = $this->CreationEtape($etapes, "ind_Disquettes", "...", ['bte_CaissetteJaune']);
                                                            $etapes = $this->CreationEtape($etapes, "egm_Disquettes", "Code 687 pour Boite à code bleu", ['ind_Disquettes', 'obj_Disquettes']);
                                                                $etapes = $this->CreationEtape($etapes, "bte_BoiteCodeBleu", "Dans la poubelle", ['egm_Disquettes']);
                                                                    $etapes = $this->CreationEtape($etapes, "obj_PinceCoupante", "...", ['bte_BoiteCodeBleu']);
  
        $etapes = $this->CreationEtape($etapes, "obj_cle_CaissetteVerte", "Dans la poche de la veste", ['StartGame']);
            $etapes = $this->CreationEtape($etapes, "bte_CaissetteVerte", "Dans une boite à archives", ['obj_cle_CaissetteVerte']);
                $etapes = $this->CreationEtape($etapes, "obj_Tournevis", "...", ['bte_CaissetteVerte']);
                $etapes = $this->CreationEtape($etapes, "obj_Cartes", "...", ['bte_CaissetteVerte']);
        $etapes = $this->CreationEtape($etapes, "obj_Gobelets", "...", ['StartGame']);
        $etapes = $this->CreationEtape($etapes, "obj_Origami", "...", ['StartGame']);
                                            $etapes = $this->CreationEtape($etapes, "egm_Loisirs", "Code 3147 pour Boite du louvre", ['obj_Cartes', 'obj_PiecesPuzzles', 'obj_Gobelets', 'obj_Origami', 'obj_Batonnets']);
                                                $etapes = $this->CreationEtape($etapes, "bte_Louvres", "...", ['bte_SacADos', 'egm_Loisirs']);
                                                    $etapes = $this->CreationEtape($etapes, "ind_Les6", "...", ['bte_Louvres']);
                                                    $etapes = $this->CreationEtape($etapes, "obj_LampeUV", "...", ['bte_Louvres']);
        $etapes = $this->CreationEtape($etapes, "obj_Crayons", "...", ['ind_Les6']);
        $etapes = $this->CreationEtape($etapes, "obj_Chaises", "...", ['ind_Les6']);
        $etapes = $this->CreationEtape($etapes, "obj_Enveloppes", "...", ['ind_Les6']);
        $etapes = $this->CreationEtape($etapes, "obj_Badges", "...", ['ind_Les6']);
                                                        $etapes = $this->CreationEtape($etapes, "egm_Les6", "Code 2347 pour petite boite blanche", ['obj_Crayons', 'obj_Chaises', 'obj_Enveloppes', 'obj_Badges', 'obj_LampeUV','ind_Les6']);
                                                            $etapes = $this->CreationEtape($etapes, "bte_PetiteBoiteBlanche", "...", ['bte_Sas', 'egm_Les6']);
                                                                $etapes = $this->CreationEtape($etapes, "obj_ManuelDemineurA", "...", ['bte_PetiteBoiteBlanche']);
                                                            
                                                                        $etapes = $this->CreationEtape($etapes, "egm_Bombe", "...", ['obj_Tournevis', 'obj_ManuelDemineurA', 'obj_ManuelDemineurB', 'obj_Pile', 'obj_Souris', 'obj_PileFace', 'obj_PinceCoupante']);
    
        //Enregistrement des étapes dans le scenario
        foreach($etapes as $etape)
        {
            $scenario->addEtape($etape);
        }

        //Sauvegarde en BDD
        $manager->persist($scenario);
        $manager->flush();
    }



































    /**
     * @Route("/escape/admin/control/trigg/{id}/{gameId}", name="escape_admin_control_trigg")
     */
    public function control_trigg(TriggRepository $repo, $id, $gameId, EntityManagerInterface $manager)
    {
        $etapes = $repo->find($id);
        $etapes->setEtat(true);
        $manager->persist($etapes);
        $manager->flush();
        return $this->redirectToRoute('exam_admin_control', ['id' => $gameId]);
    }

    /**
     * @Route("/escape/admin/control/{id}", name="escape_admin_control")
     */
    public function control(GameRepository $repo, $id)
    {
        $game = $repo->find($id);

        $nextTriggs = $game->getScenario()->NextTriggs();

        return $this->render('exam_admin/control.html.twig', [
            'game' => $game,
            'nextTriggs' => $nextTriggs,
        ]);
    }
}
