<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Form\ScanType;
use App\Repository\FilRepository;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Nombre de minutes entre le début de la partie et le déclenchement de la bombe
define("MINUTES_INTRO", "1");

class ExamController extends AbstractController
{
    private $twig;
    private $paramTwig = array();
    private $triggOn = array();

    /**
     * @Route("/escape", name="escape")
     * 
     * Affiche la liste des parties
     */
    public function index(GameRepository $repo)
    {
       $games = $repo->findAll();
        
        return $this->render('exam/index.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("escape/exam/{id}", name="escape_exam")
     */
    public function exam($id, GameRepository $repo, Request $request, EntityManagerInterface $manager)
    {
        //Création d'un formulaire pour l'utilisation du scanner
        $form = $this->createForm(ScanType::class);        
        $form->handleRequest($request);
        
        //Récupération de la partie en cours
        $game = $repo->find($id);

        //Gestion du temps
        $SecondesDeJeu = $game->calculDureeDeJeu();
       
        /*******************************************/
        /* --- AVANT LE LANCEMENT DE LA PARTIE --- */
        /*******************************************/

        dump($game);
        die();

        //Si la partie n'est pas démarré
        if($this->trigger($game, "StartGame") != true)
        {
            //Affichage de la page de lancement de la partie
            $this->defineTwig('exam/pregame.html.twig');
        }
        else
        {
            /*******************************************/
            /* --- AVANT L'INTRO ---                   */
            /*******************************************/
    
            //Si l'intro n'est pas encore lu
            if($this->trigger($game, "VisionageIntro") != true)
            {
                //Si durée de jeu <= MINUTES_INTRO on affiche l'horloge classique
                if($SecondesDeJeu <= MINUTES_INTRO * 60)
                {
                    //affichage de l'horloge
                    $this->defineTwig('exam/horloge.html.twig');
                    $this->defineParamTwig("duree", $SecondesDeJeu); 
                }
                //Sinon on lance l'intro
                else
                {
                    //Lancement de la vidéo d'intro
                    $this->video("intro");
                    $this->defineTriggOn("VisionageIntro");                    
                }
            }
            else
            {
                /*******************************************/
                /* --- PARTIE EN COURS ---                 */
                /*******************************************/
                
                //Si la bombe n'est pas encore mise en route on la démarre
                if($this->trigger($game, "StartBombe") != true)
                {
                    $game->StartBombe();
                    $this->defineTriggOn("StartBombe"); 
                }

                //Le compte a rebours arrive juste à 0
                if($this->trigger($game, "Boum") != true && $game->calculDureeBombe() <= 0)
                {
                    $this->defineTwig('exam/game.html.twig');
                    $this->defineTriggOn("Boum"); 
                }
                else
                {
                    //Si la bombe est encore active
                    if($this->trigger($game, "DesamorcageRate") != true && $this->trigger($game, "DesamorcageReussi") != true && $this->trigger($game, "Boum") != true)
                    {
                        //Action a effectuer selon le scan
                        $game = $this->gestionScan($game, $form);

                        //Affichage de la bombe
                        $this->defineTwig('exam/game.html.twig');
                        $this->defineParamTwig("duree", $SecondesDeJeu - (MINUTES_INTRO * 60));
                        $this->defineParamTwig("dureebombe", $game->calculDureeBombe());
                        $this->defineParamTwig("form", $form->createView());
                    }
                    else
                    {
                        //Changement d'état de la bombe, on lance la video adapté
                        if($this->trigger($game, "FinGame") != true)
                        {
                            if($this->trigger($game, "DesamorcageReussi") == true)
                            {
                                $this->video("gagne"); 
                            }
                            else
                            {
                                $this->video("perdu");
                            }

                            //On déclare la fin de partie
                            $this->defineTriggOn("FinGame"); 
                        }
                        else
                        {
                            //Ecran de fin
                            $this->defineTwig('exam/fin.html.twig');
                        }
                    }
                }
                
            }
        }

        /*******************************************/
        /* --- FIN DE SCRIPT ---                   */
        /*******************************************/
        
        return $this->SaveShow($manager, $game);
    }

    /**
     * @Route("/exam/game/{id}/start", name="game_start")
     */
    public function gameStart($id, GameRepository $repo, EntityManagerInterface $manager)
    {
        $game = $repo->find($id);
        $game->Debuter();
        $this->triggOn($game, "StartGame");
        $manager->persist($game);
        $manager->flush();
        return $this->redirectToRoute('game', ['id' => $game->getId()]);
    }

    /**
     * @Route("/exam/game/{id}/coupe/{cutId}", name="game_cut")
     */
    public function gameCut($id, $cutId, GameRepository $repo, FilRepository $repoFil, EntityManagerInterface $manager)
    {
        $game = $repo->find($id);
        
        //Verification si la pince est activée
        if($game->getBombe()->getPince() == true)
        {
            //Si il reste plus de 2 fils on coupe !
            if(sizeof($game->getBombe()->FilsRestants()) != 2)
            {
                //On coupe le fil
                if($cutId == $game->getBombe()->filACouper())
                {
                    $fil = $repoFil->find($cutId);
                    $fil->setEtat(0);
                    $manager->persist($fil);            
                }
                else
                {
                    //Perdu //Un mauvais fil a été coupé...
                    $this->triggOn($game, "DesamorcageRate");                     
                }
            }
            else
            {
                //Gagné - On coupe le dernier fil est c'est gagné
                $this->triggOn($game, "DesamorcageReussi");
            }

            $manager->persist($game);
            $manager->flush();
        }        
        return $this->redirectToRoute('game', ['id' => $game->getId()]);
    }

    private function gestionScan($game, $form)
    {
        //Action a effectuer selon le code barre
        $code = $this->Scan($form);
        switch($code)
        {
            //Code barre du tournevis
            case '020312':
            case '020310':
                $game->getBombe()->devisser();
                if($game->getBombe()->VisRestantes() == 0)
                {
                    $this->triggOn($game, "VisSupprimees"); 
                }
            break;
            //Code barre de la pince
            case '020311':
            case '020309':
                $game->getBombe()->setPince(1);
                $this->triggOn($game, "PinceActive");
            break;
            case null:
            break;           
        }
        return $game;
    }

    private function Scan($form)
    {
        $scan = null;
        if($form->isSubmitted() && $form->isValid()) 
        {
            $scan = $form['scan']->getData();            
        }
        return $scan;
    }

    /**
     * Affiche une video
     */
    private function video($video)
    {
        $this->defineTwig("exam/video.html.twig");
        $this->defineParamTwig("video", $video);
    }

    /*****************/
    /*** FONCTIONS ***/
    /*****************/

    /**
     * Vérifie l'état d'un trigg et retour true or false
     */
    private function trigger($game, $titre)
    {
        foreach($game->getScenario()->getTriggs() as $trigg)
        {
            if($trigg->getTitre() == $titre)
            {
                return $trigg->getEtat();
            }
        }
        return null;        
    }

    /**
     * Active un trigg
     */
    private function triggOn($game, $titre)
    {
        foreach($game->getScenario()->getTriggs() as $trigg)
        {
            if($trigg->getTitre() == $titre)
            {
                $trigg->setEtat(true);
            }
        }
    }

    private function defineTwig($twig)
    {
        $this->twig = $twig;
    }
    private function defineParamTwig($cle, $valeur)
    {
        $this->paramTwig[$cle] = $valeur;
    }
    private function defineTriggOn($cle)
    {
        array_push($this->triggOn, $cle);
    }
    
    private function SaveShow($manager, $game)
    {
        //Active les triggs
        foreach($this->triggOn as $trigg)
        {
            $this->triggOn($game, $trigg);
        }
        //Sauvegarde de la partie
        $manager->persist($game);
        $manager->flush();
        //Affiche la page
        $this->defineParamTwig('game', $game);
        return $this->render($this->twig, $this->paramTwig);
    }
}
