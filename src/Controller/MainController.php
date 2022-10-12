<?php


namespace App\Controller;

use App\Data\SearchData;
use App\Form\FiltreType;
use App\Repository\ProduitRepository;
use App\Repository\SousCategorieRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class MainController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    /**
     * @Route ("/", name = "Accueil")
     */
    public function home (UtilisateurRepository $utilisateurRepository, AuthenticationUtils $authenticationUtils,Request $request, ProduitRepository $produitRepository, SousCategorieRepository $sousCategorieRepository)  {


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
        $i=0;

        $produitMap= [];
        foreach ($produits as $produit) {

            $produitMap[$i] = ['nomArticle'=>$produit->getNomarticle(), 'longitude'=>$produit->getEntreprise()->getVille()->getLongitude(), 'latitude'=> $produit->getEntreprise()->getVille()->getLatitude()
            , 'urlFichier'=>!empty($produit->getFichiers()[0])?$produit->getFichiers()[0]->getUrlFichier():"notFound.png"] ;

            $i++;
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


        return $this->render('Accueil.html.twig', ['produits' => $produits, 'filtreFormulaire' => $filtreFormulaire->createView(), 'produitMap' => $produitMap] );

    }

    /**
     * @Route ("/Admin", name = "Administration")
     */
    public function admin ()  {

        return $this->render('Admins/AdministrationSite.html.twig')  ;
    }

    /**
     * @Route ("/Conditions_générales_de_vente", name ="CGV")
     */
    public function cgv(){
        return $this->render('legislatif/cgv.html.twig');
    }

    /**
     * @Route ("/Mentions_legales", name ="ML")
     */
    public function mentions(){
        return $this->render('legislatif/mentions_legales.html.twig');
    }

}
