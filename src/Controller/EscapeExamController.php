<?php

namespace App\Controller;

use App\Form\ScanType;
use App\Repository\FilRepository;
use App\Repository\GameRepository;
use App\Controller\EscapeController;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Nombre de minutes entre le début de la partie et le déclenchement de la bombe
define("MINUTES_INTRO", "4");
define("CODEBARRE_ADMIN", "020312");
define("CODEBARRE_TOURNEVIS_1", "020312");
define("CODEBARRE_TOURNEVIS_2", "020310");
define("CODEBARRE_PINCE_1", "020311");
define("CODEBARRE_PINCE_2", "020309");
define("CODEBARRE_VISITEUR_1", "020251");
define("CODEBARRE_VISITEUR_2", "020252");
define("CODEBARRE_VISITEUR_3", "020253");
define("CODEBARRE_VISITEUR_4", "020254");
define("CODEBARRE_VISITEUR_5", "020255");
define("CODEBARRE_VISITEUR_6", "020256");

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
    public function exam($id, GameRepository $repo, JoueurRepository $repoJoueur, Request $request, EntityManagerInterface $manager)
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
            $formsLogJoueurs = $this->createForm(ScanType::class);        
            $formsLogJoueurs->handleRequest($request);
            //Affichage de la page de lancement de la partie
            $this->defineParamTwig("form", $formsLogJoueurs->createView());
            $this->defineTwig('escape/exam/pregame.html.twig');

            //On regarde qui essaye de se logguer
            $game = $this->LogJoueur($game, $this->Scan($formsLogJoueurs));
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
                            $game = $this->EtatJoueursFindePartie($game);
                        }
                        else
                        {
                            //Ecran de fin
                            $game = $this->calculStats($game, $repoJoueur);
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
                $bombe->setDureeFin($bombe->calculDureeBombe());
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
     * Affiche les stats sur la page de fin
     *
     * @param [type] $game
     * @return void
     */
    private function calculStats($game, $repoJoueur)
    {
        $joueurs = $repoJoueur->findAll();
        $stats = [];
        $stats['rate'] = 0;
        $stats['reussi'] = 0;
        $stats['boum'] = 0;
        $stats['sas'] = 0;
        $stats['lache'] = 0;

        foreach($joueurs as $joueur)
        {
            if($joueur->getGame()->getScenario()->getCode() == "exam")
            {
                $etat = $joueur->getEtat();
                switch($etat)
                {
                    case 'rate':
                    case 'reussi':
                    case 'boum':
                    case 'sas':
                    case 'lache':
                        $stats[$etat] = $stats[$etat] + 1;
                    break;
                }
            }
        }
        $this->defineParamTwig("stats", $stats);
        return $game;
    }

    /**
     * Permet de donner un état automatiquement à chacun des joueurs en fin de partie
     *
     * @param [type] $game
     * @return void
     */
    private function EtatJoueursFindePartie($game)
    {
        $joueurs = $game->getJoueurs();
        if($game->etatCommut('DesamorcageRate'))
        {
            foreach($joueurs as $joueur)
            {
                $joueur->setEtat('rate');
            }
        }
        if($game->etatCommut('DesamorcageReussi'))
        {
            foreach($joueurs as $joueur)
            {
                $joueur->setEtat('reussi');
            }
        }
        if($game->etatCommut('Boum'))
        {
            foreach($joueurs as $joueur)
            {
                $joueur->setEtat('boum');
            }
        }
        return $game;
    }

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
        //On récupère l'aide
        $aide = $game->rechercheObjetScenario('aide');

        //Action a effectuer selon le code barre
        $code = $this->Scan($form);
        switch($code)
        {
            //Code barre du tournevis
            case CODEBARRE_TOURNEVIS_1:
            case CODEBARRE_TOURNEVIS_2:
                $bombe->devisser();
                if($bombe->BoulonsRestantes() == 0)
                {
                    $game->onCommut('BoulonsSupprimees'); 
                }
            break;
            //Code barre de la pince
            case CODEBARRE_PINCE_1:
            case CODEBARRE_PINCE_2:
                $bombe->setPince(1);
                $game->onCommut('PinceActive');
            break;
            //Code barre des badges
            case CODEBARRE_VISITEUR_1:
            case CODEBARRE_VISITEUR_2:
            case CODEBARRE_VISITEUR_3:
            case CODEBARRE_VISITEUR_4:
            case CODEBARRE_VISITEUR_5:
            case CODEBARRE_VISITEUR_6:
                //Affiche un message Flash
                $this->addFlash('messageIndice', $aide->demanderIndice($code));
            break;
            case null:
            break;           
        }
        return $game;
    }

    private function LogJoueur($game, $codeBarre)
    {
        $joueurs = $game->getJoueurs();
        $nbrJoueursPrets = 0;
        $nbrJoueurs = sizeof($joueurs);
        $numTable = ['1', '4', '5', '2', '6', '3'];

        foreach($joueurs as $joueur)
        {
            if($joueur->getEtat() == "pret")
            {
                $nbrJoueursPrets = $nbrJoueursPrets + 1;
            }
        }

        if($nbrJoueursPrets < $nbrJoueurs)
        {
            foreach($joueurs as $joueur)
            {
                if($joueur->getCodeBarre() == $codeBarre)
                {
                    $joueur->setEtat('pret');
                    $this->addFlash('log', $joueur->getPrenom()." installez vous à la table n° ".$numTable[$nbrJoueursPrets]." !");
                }
            }
            if($nbrJoueursPrets == $nbrJoueurs)
            {
                $this->OnCommut('JoueursPrets');
            }
        }
        else
        {
            //Lancement de la partie par l'admin
            if($codeBarre == CODEBARRE_ADMIN)
            {
                $game->debuter();
                $game->onCommut('StartGame');
            }
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