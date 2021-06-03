<?php


namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    /**
     * @Route ("/", name = "Accueil")
     */
    public function home (UtilisateurRepository $utilisateurRepository)  {


        
        return $this->render('Accueil.html.twig')  ;
    }

    /**
     * @Route ("/Admin", name = "Administration")
     */
    public function admin ()  {

        return $this->render('Admins/AdministrationSite.html.twig')  ;
    }


}
