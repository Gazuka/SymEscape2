<?php

namespace App\Controller;

use DateTime;
use App\Entity\Game;
use App\Entity\Etape;
use App\Entity\Commut;
use App\Form\GameType;
use App\Entity\Scenario;
use App\Entity\ObjetScenario;
use App\Form\ScenarioEditType;
use App\Repository\GameRepository;
use App\Repository\EtapeRepository;
use App\Controller\OutilsController;
use App\Repository\IndiceRepository;
use App\Repository\JoueurRepository;
use App\Repository\ScenarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class EscapeAdminController extends OutilsController
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
     * @Route("/escape/admin/control/{id}", name="escape_admin_control")
     * Page utilisé par le surveillant pour indiquer les objets actuellements trouvés
     */
    public function control(GameRepository $repo, $id)
    {
        $game = $repo->find($id);

        $commutsDeblocables = $game->commutsDeblocables();

        return $this->render('escape_admin/control.html.twig', [
            'game' => $game,
            'commutsDeblocables' => $commutsDeblocables,
        ]);
    }

    /**
     * @Route("/escape/admin/control/{id}/commuton/{commut}", name="escape_admin_control_commuton")
     * Page utilisé par le surveillant pour indiquer les objets actuellements trouvés
     */
    public function controlOnCommut($id, $commut, GameRepository $repo, EntityManagerInterface $manager)
    {
        $game = $repo->find($id);
        if($game->rechercheCommutTitre($commut)->getEtape()->getAutomatique() == false)
        {
            $game->onCommut($commut);
        }        
        $manager->persist($game);
        $manager->flush();
        return $this->redirectToRoute('escape_admin_control', ['id' => $game->getId()]);
    }

    /**
     * @Route("/escape/admin/scenarios", name="escape_admin_scenarios")
     * Affiche le listing des scénarios
     */
    public function afficherScenarios(ScenarioRepository $repo, Request $request):Response
    {
        $scenarios = $repo->findAll();

        return $this->render('escape_admin/scenarios.html.twig', [
            'scenarios' => $scenarios,
        ]);
    }

    /**
     * @Route("/escape/admin/scenario/edit/{id}", name="escape_admin_scenario_edit")
     * Affiche la page d'édition d'un scenario'
     */
    public function editScenario(ScenarioRepository $repo, $id, EtapeRepository $repoEtape, IndiceRepository $repoIndice, Request $request, EntityManagerInterface $manager):Response
    {
        $scenario = $repo->find($id);

        $variables['request'] = $request;
        $variables['manager'] = $manager;
        $variables['element'] = $scenario;
        $variables['classType'] = ScenarioEditType::class;
        $variables['pagedebase'] = 'escape_admin/editscenario.html.twig';
        $variables['pagederesultat'] = 'escape_admin';
        $variables['titre'] = "Edition d'un scenario";
        $variables['texteConfirmation'] = "Le scenario ### a bien été éditée !";
        $options['dependances'] = array('Etapes' => 'Scenario');  
        $options['deletes'] = array(['findBy' => 'scenario', 'classEnfant' => 'etapes', 'repo' => $repoEtape]); 
        $options['texteConfirmationEval'] = ["###" => '$element->getTitre();'];
        return $this->afficherFormulaire($variables, $options);
    }

    /**
     * @Route("/escape/admin/scenario/{id}", name="escape_admin_scenario")
     * Affiche un scenario'
     */
    public function afficherScenario(ScenarioRepository $repo, $id, Request $request):Response
    {
        $scenario = $repo->find($id);

        return $this->render('escape_admin/scenario.html.twig', [
            'scenario' => $scenario,
        ]);
    }

    //-----------------------------------------------------------------------------------
    //Fonctions abstraites ==============================================================
    //-----------------------------------------------------------------------------------

    /**
     * Affiche la page de modification d'une partie
     */
    abstract public function editGame(GameRepository $repo, $id, JoueurRepository $repoJoueur, Request $request, EntityManagerInterface $manager):Response;

    /**
     * Permet l'initialisation d'un nouveau scenario en BDD
     */
    abstract public function firstinit(EntityManagerInterface $manager);

    //-----------------------------------------------------------------------------------
    // ACTIONS A UTILIERS LORS DE LA VALIDATION D'UN FORMULAIRE =========================
    //-----------------------------------------------------------------------------------

    /**
     * ACTION : Base pour l'initialisation d'un scénario dans une partie
     */
    protected function action_initScenario(Object $game, $params, $request)
    {
        //Assignation des étapes dans la partie
        foreach($game->getScenario()->getEtapes() as $etape)
        {
            $commut = new Commut();
            $commut->setEtape($etape);
            $commut->setEtat(false);
            $commut->setDeblocable(false); // A modifier
            $game->addCommut($commut);
        }
        return $game;
    }

    /**
     * ACTION : Choisi l'action lors d'un edit en fonction du scénario
     */
    protected function action_editScenario(Object $game, $params, $request)
    {
        return $game;
    }    
    
    //-----------------------------------------------------------------------------------
    // FONCTIONS DE SIMPLIFICATION EN PROTECTED =========================================
    //-----------------------------------------------------------------------------------
    /**
     * Création d'une étape obligatoirment sur false en début de partie
     */
    protected function CreationEtape($etapes, $titre, $descriptif, $parents = null, $automatique = false)
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
}