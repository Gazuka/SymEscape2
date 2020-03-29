<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EscapeController extends AbstractController
{
    private $twig;
    private $paramTwig = array();
    
    //-----------------------------------------------------------------------------------
    //Fonctions avec route ==============================================================
    //-----------------------------------------------------------------------------------

    /**
     * @Route("/escape", name="escape")
     * 
     * N'est plus utilisé pour l'instant...
     * Affiche un listing des parties en cours ou en attente de démarrage
     */
    public function index(GameRepository $repo)
    {
       $games = $repo->findAll();
        
        return $this->render('escape/index.html.twig', [
            'games' => $games,
        ]);
    }

    /****************************************************************************************/
    /*** FONCTIONS GENERIQUES ***************************************************************/
    /****************************************************************************************/

    /**
     * Retourne les données scannées par le lecteur de code barre
     *
     * @param [type] $form
     * @return void
     */
    protected function Scan($form)
    {
        $scan = null;
        if($form->isSubmitted() && $form->isValid()) 
        {
            $scan = $form['scan']->getData();       
        }
        return $scan;
    }

    /**
     * Permet de définir le Twig qui sera utilisé lors de l'affichage
     *
     * @param [string] $twig //Chemin du twig
     * @return void
     */
    protected function defineTwig($twig)
    {
        $this->twig = $twig;
    }

    /**
     * Permet de définit les paramètres utiles au Twig lors de l'affichage
     *
     * @param [string] $cle //Nom de la variable dans le twig
     * @param [type] $valeur //Données qui seront utilisées dans le twig
     * @return void
     */
    protected function defineParamTwig($cle, $valeur)
    {
        $this->paramTwig[$cle] = $valeur;
    }

    /**
     * Sauvegarde la partie et affiche la page requise
     *
     * @param [ObjectManager] $manager
     * @param [Game] $game
     * @return void
     */
    protected function SaveShow($manager, $game)
    {
        //Sauvegarde de la partie
        $manager->persist($game);
        $manager->flush();
        //Affiche la page
        $this->defineParamTwig('game', $game);
        return $this->render($this->twig, $this->paramTwig);
    }
}
