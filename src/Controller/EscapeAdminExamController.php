<?php

namespace App\Controller;

use App\Entity\Fil;
use App\Entity\Boulon;
use App\Entity\Aide;
use App\Entity\Bombe;
use App\Entity\Scenario;
use App\Form\GameEditType;
use App\Entity\ObjetScenario;
use App\Form\GameEditExamType;
use App\Repository\GameRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\EscapeAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EscapeAdminExamController extends EscapeAdminController
{
    //-----------------------------------------------------------------------------------
    //Fonctions avec route ==============================================================
    //-----------------------------------------------------------------------------------
    
    /**
     * @Route("/escape/admin/exam/firstinit", name="escape_admin_exam_firstinit")
     * Page à executer pour l'installation du nouveau scenario
     */
    public function firstinit(EntityManagerInterface $manager)
    {
        //Initialisation des scénario si bdd vide (laisser en commentaire)
        $this->CreationScenario($manager);

        return $this->redirectToRoute('escape_admin');
    }

    /**
     * @Route("/escape/admin/exam/edit/{id}", name="escape_admin_exam_edit")
     * Affiche la page de création d'une nouvelle partie
     */
    public function editGame(GameRepository $repo, $id, JoueurRepository $repoJoueur, Request $request, EntityManagerInterface $manager):Response
    {
        $game = $repo->find($id);

        $variables['request'] = $request;
        $variables['manager'] = $manager;
        $variables['element'] = $game;
        $variables['classType'] = GameEditExamType::class;
        $variables['pagedebase'] = 'escape_admin/exam/editgame.html.twig';
        $variables['pagederesultat'] = 'escape_admin';
        $variables['titre'] = "Edition d'une partie";
        $variables['texteConfirmation'] = "La partie ### a bien été éditée !";
        $options['dependances'] = array('Joueurs' => 'Game');  
        $options['deletes'] = array(['findBy' => 'game', 'classEnfant' => 'joueurs', 'repo' => $repoJoueur]);  
        $options['texteConfirmationEval'] = ["###" => '$element->getId();'];
        $options['actions'] = array(['name' => 'action_editScenario', 'params' => []]);
        return $this->afficherFormulaire($variables, $options);
    }

    /**
     * @Route("/escape/admin/exam/control/etat/joueur/{id}/{etat}/{idjoueur}", name="escape_admin_exam_control_etat_joueur")
     * Modification de l'état d'un joueur
     */
    public function editEtatJoueur($id, $etat, $idjoueur, GameRepository $repo, EntityManagerInterface $manager)
    {
        $game = $repo->find($id);
        foreach($game->getJoueurs() as $joueur)
        {
            if($joueur->getId() == $idjoueur)
            {
                if($etat != 'auto')
                {
                    $joueur->setEtat($etat);
                }
                else
                {
                    if($game->etatCommut('DesamorcageReussi') == true)
                    {
                        $joueur->setEtat('reussi'); 
                    }
                    if($game->etatCommut('DesamorcageRate') == true)
                    {
                        $joueur->setEtat('rate'); 
                    }
                    if($game->etatCommut('Boum') == true)
                    {
                        $joueur->setEtat('boum'); 
                    }
                }
                
            }
        }
        
        $manager->persist($game);
        $manager->flush();
        return $this->redirectToRoute('escape_admin_control', ['id' => $game->getId()]);
    }

    //-----------------------------------------------------------------------------------
    // ACTIONS A UTILIERS LORS DE LA VALIDATION D'UN FORMULAIRE =========================
    //-----------------------------------------------------------------------------------
    /**
     * ACTION : Base pour l'initialisation d'un scénario dans une partie
     */
    protected function action_initScenario(Object $game, $params, $request)
    {
        $game = parent::action_initScenario($game, $params, $request);
        
        //Création d'un objet pour le scénario
        $objetBombe = new ObjetScenario();
        $objetBombe->setNom('bombe');
        //Création de la bombe
        $objetBombe->setBombe($this->_CreateBombe());
        //Ajout de l'objetScenario dans la partie
        $game->addObjetsScenario($objetBombe);

        //Création d'un objet pour le scénario
        $objetAide = new ObjetScenario();
        $objetAide->setNom('aide');
        //Création de la bombe
        $objetAide->setAide(new Aide());
        //Ajout de l'objetScenario dans la partie
        $game->addObjetsScenario($objetAide);

        return $game;
    }

    /**
     * ACTION : Edition du scénario
     */
    protected function action_editScenario(Object $game, $params, $request)
    {
        $game = parent::action_editScenario($game, $params, $request);

        foreach($game->getObjetsScenario() as $objet)
        {
            //Permet d'éditer la durée de la bombe
            if($objet->getBombe() != null)
            {
                $duree = $request->request->get('game_edit_exam')['duree'];
                if($duree != null)
                {
                    $objet->getBombe()->setDuration($duree*60);
                }
            }
        }
        return $game;
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
        //Création des 4 boulons
        for($i=1; $i<=4; $i++)
        {
            $boulon = new Boulon();
            $boulon->setEtat(true);
            $bombe->addBoulon($boulon);
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
    private function CreationScenario($manager)
    {
        //Création du scnenario
        $scenario = new Scenario();
        $scenario->setCode("exam");
        $scenario->setTitre("L'exam !");
        $etapes = Array();

        //Création des différents étapes automatiques du scenario
        $etapes = $this->CreationEtape($etapes, "JoueursPrets", "Les jours sont installés", null, true);
        $etapes = $this->CreationEtape($etapes, "StartGame", "La partie est débuté", null, true);
         $etapes = $this->CreationEtape($etapes, "VisionageIntro", "Visualisation de la vidéo d'introduction", ['StartGame'], true);
          $etapes = $this->CreationEtape($etapes, "StartBombe", "Mise en route de la bombe", ['VisionageIntro'], true);
           $etapes = $this->CreationEtape($etapes, "PinceActive", "La pince est activé sur la bombe", ['StartBombe'], true);
           $etapes = $this->CreationEtape($etapes, "BoulonsSupprimees", "Les 4 boulons sont retirées", ['StartBombe'], true);
            $etapes = $this->CreationEtape($etapes, "DesamorcageRate", "Vous avez coupé le mauvais fil...", ['PinceActive', 'BoulonsSupprimees'], true);
            $etapes = $this->CreationEtape($etapes, "DesamorcageReussi", "Vous avez coupé le mauvais fil...", ['PinceActive', 'BoulonsSupprimees'], true);
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
}
