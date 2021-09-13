<?php


namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/Administration")
 */
class AdminController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController

{
    /**
     * @Route("/adminBoard", name="adminBoard", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('Admins/AdministrationSite.html.twig');
    }

}
