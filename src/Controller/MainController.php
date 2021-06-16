<?php


namespace App\Controller;

use App\Repository\ProduitRepository;
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
    public function home (UtilisateurRepository $utilisateurRepository, AuthenticationUtils $authenticationUtils, ProduitRepository $produitRepository)  {

        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $produits = $produitRepository->findAll();
        $data = new SearchData();

        $filtreFormulaire = $this->createForm(FiltreType::class, $data);
        $filtreFormulaire->handleRequest($request);
//        Traitement du formulaire de filtre

        if ($filtreFormulaire->isSubmitted() && $filtreFormulaire->isValid()) {

            $produits = $produitRepository->findSearch($data);

        } else {
            $produits = $produitRepository->findAll();
        }


        //ajax
        if ($request->get('ajax') && $request->get('categorie')) {

            $categorieString = $request->get('categorie');
            $categorie = (int)$categorieString;
            $sousCategories = $sousCategorieRepository->getSousCategorieByCategorieIdAjax($categorie);
        return new JsonResponse([
            'content' => $this->renderView('categorie/_selectCategorie.html.twig', compact('sousCategories'))
        ]);
    }

        return $this->render('Accueil.html.twig', ['produits' => $produits]);

    }

    /**
     * @Route ("/Admin", name = "Administration")
     */
    public function admin ()  {

        return $this->render('Admins/AdministrationSite.html.twig')  ;
    }


}
