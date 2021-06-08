<?php


namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    /**
     * @Route ("/", name = "Accueil")
     */
    public function home (UtilisateurRepository $utilisateurRepository, AuthenticationUtils $authenticationUtils)  {

        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Accueil.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

    }

    /**
     * @Route ("/Admin", name = "Administration")
     */
    public function admin ()  {

        return $this->render('Admins/AdministrationSite.html.twig')  ;
    }


}
