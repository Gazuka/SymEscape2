<?php

namespace App\Controller;

use App\Form\ScanType;
use App\Repository\FilRepository;
use App\Repository\GameRepository;
use App\Controller\EscapeController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Nombre de minutes entre le début de la partie et le déclenchement de la bombe
define("MINUTES_INTRO", "1");

class EscapeExamController extends EscapeController
{
    //-----------------------------------------------------------------------------------
    //Fonctions avec route ==============================================================
    //-----------------------------------------------------------------------------------

    /**
     * @Route("escape/exam/{id}", name="escape_exam")
     * 
     * La page qui gère la partie en cours
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

        //Si la partie n'est pas démarré
        if($game->etatCommut('StartGame') != true)
        {
            //Affichage de la page de lancement de la partie
            $this->defineTwig('escape/exam/pregame.html.twig');
        }
        else
        {
            /*******************************************/
            /* --- AVANT L'INTRO ---                   */
            /*******************************************/
    
            //Si l'intro n'est pas encore lu
            if($game->etatCommut('VisionageIntro') != true)
            {
                //Si durée de jeu <= MINUTES_INTRO on affiche l'horloge classique
                if($SecondesDeJeu <= MINUTES_INTRO * 60)
                {
                    //affichage de l'horloge
                    $this->defineTwig('escape/exam/horloge.html.twig');
                    $this->defineParamTwig('duree', $SecondesDeJeu); 
                }
                //Sinon on lance l'intro
                else
                {
                    //Lancement de la vidéo d'intro
                    $this->video('intro');
                    $game->onCommut('VisionageIntro');                    
                }
            }
            else
            {
                /*******************************************/
                /* --- PARTIE EN COURS ---                 */
                /*******************************************/
                
                //On récupère la bombe
                $bombe = $game->rechercheObjetScenario('bombe');

                //Si la bombe n'est pas encore mise en route on la démarre
                if($game->etatCommut('StartBombe') != true)
                {
                    $bombe->StartBombe();
                    $game->onCommut('StartBombe'); 
                }

                //Le compte a rebours arrive juste à 0
                if($game->etatCommut('Boum') != true && $bombe->calculDureeBombe() <= 0)
                {
                    $this->defineTwig('escape/exam/game.html.twig');
                    $this->defineParamTwig("form", $form->createView());
                    $this->defineParamTwig("duree", $SecondesDeJeu - (MINUTES_INTRO * 60));
                    $this->defineParamTwig("dureebombe", $bombe->calculDureeBombe());
                    $game->onCommut('Boum'); 
                }
                else
                {
                    //Si la bombe est encore active
                    if($game->etatCommut('DesamorcageRate') != true && $game->etatCommut('DesamorcageReussi') != true && $game->etatCommut('Boum') != true)
                    {
                        //Action a effectuer selon le scan
                        $game = $this->gestionScan($game, $form);

                        //Affichage de la bombe
                        $this->defineTwig('escape/exam/game.html.twig');
                        $this->defineParamTwig("duree", $SecondesDeJeu - (MINUTES_INTRO * 60));
                        $this->defineParamTwig("dureebombe", $bombe->calculDureeBombe());
                        $this->defineParamTwig("form", $form->createView());
                    }
                    else
                    {
                        //Changement d'état de la bombe, on lance la video adapté
                        if($game->etatCommut('FinGame') != true)
                        {
                            if($game->etatCommut('DesamorcageReussi') == true)
                            {
                                $this->video('gagne'); 
                            }
                            else
                            {
                                $this->video('perdu');
                            }

                            //On déclare la fin de partie
                            $game->onCommut('FinGame'); 
                        }
                        else
                        {
                            //Ecran de fin
                            $this->defineTwig('escape/exam/fin.html.twig');
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
     * @Route("/escape/exam/{id}/start", name="escape_exam_start")
     * 
     * Page qui démarre simplement la partie puis se redirige vers la page du jeu
     */
    public function gameStart($id, GameRepository $repo, EntityManagerInterface $manager)
    {
        $game = $repo->find($id);
        $game->debuter();
        $game->onCommut('StartGame');
        $manager->persist($game);
        $manager->flush();
        return $this->redirectToRoute('escape_exam', ['id' => $game->getId()]);
    }

    /**
     * @Route("/escape/exam/{id}/coupe/{cutId}", name="escape_exam_cut")
     */
    public function gameCut($id, $cutId, GameRepository $repo, FilRepository $repoFil, EntityManagerInterface $manager)
    {
        $game = $repo->find($id);
        
        //On récupère la bombe
        $bombe = $game->rechercheObjetScenario('bombe');

        //Verification si la pince est activée
        if($bombe->getPince() == true)
        {
            //Si il reste plus de 2 fils on coupe !
            if(sizeof($bombe->FilsRestants()) != 2)
            {
                //On coupe le fil
                if($cutId == $bombe->filACouper())
                {
                    $fil = $repoFil->find($cutId);
                    $fil->setEtat(0);
                    $manager->persist($fil);            
                }
                else
                {
                    //Perdu //Un mauvais fil a été coupé...
                    $game->onCommut('DesamorcageRate');                     
                }
            }
            else
            {
                //Gagné - On coupe le dernier fil est c'est gagné
                $game->onCommut('DesamorcageReussi');
            }

            $manager->persist($game);
            $manager->flush();
        }        
        return $this->redirectToRoute('escape_exam', ['id' => $game->getId()]);
    }

    /****************************************************************************************/
    /*** FONCTIONS SPECIFIQUES **************************************************************/
    /****************************************************************************************/

    /**
     * Choisi les actions à faire en fonction du scan par code barre
     *
     * @param [Game] $game
     * @param [type] $form
     * @return void
     */
    private function gestionScan($game, $form)
    {
        //On récupère la bombe
        $bombe = $game->rechercheObjetScenario('bombe');

        //Action a effectuer selon le code barre
        $code = $this->Scan($form);
        switch($code)
        {
            //Code barre du tournevis
            case '020312':
            case '020310':
                $bombe->devisser();
                if($bombe->VisRestantes() == 0)
                {
                    $game->onCommut('VisSupprimees'); 
                }
            break;
            //Code barre de la pince
            case '020311':
            case '020309':
                $bombe->setPince(1);
                $game->onCommut('PinceActive');
            break;
            case null:
            break;           
        }
        return $game;
    }

    /****************************************************************************************/
    /*** FONCTIONS GENERIQUES ***************************************************************/
    /****************************************************************************************/

    /**
     * Permet de demander la lecture d'une vidéo
     *
     * @param [string] $video //Nom de la vidéo
     * @return void
     */
    private function video($video)
    {
        $this->defineTwig("escape/exam/video.html.twig");
        $this->defineParamTwig("video", $video);
    }
}