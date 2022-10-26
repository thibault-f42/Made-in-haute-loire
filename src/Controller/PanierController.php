<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier_index")
     */
    public function index(ProduitRepository $produitRepository, SessionInterface $session): Response
    {

        $panier= $session->get("panier", []);

        //on initialise le tableau de produits
        $dataPanier = [];
        $total = 0 ;

        foreach ($panier as $id => $quantite)
        {
            $produit = $produitRepository->find($id);
            $dataPanier[]= ["produit" => $produit, "quantite" => $quantite];

            $total  += $produit->getPrix() * $quantite;
        }

        return $this->render('Panier/index.html.twig', compact("dataPanier", "total"));
    }

    /**
     * @Route("/add/{id}/{provenance}", name="panier_add")
     */
    public function ajoutProduit(Produit $produit, SessionInterface $session, String $provenance): Response
    {
//        récupération du panier

        $panier = $session->get("panier", []);
        $id = $produit->getId();
        if(!empty($panier[$id])){
            $panier[$id]++;
            $this->addFlash('message', "L'article a bien été ajouté au
            panier !");
            return $this->redirectToRoute('Accueil',['id'=> $produit->getId()]);
        }
        else{
            $panier[$id] = 1;
        }

//        on enregistre ça en session
        $session->set("panier", $panier);

        return $this->redirectToRoute($provenance);
    }

    /**
     * @Route("/remove/{id}/{provenance}", name="panier_remove")
     */
    public function retireProduit(Produit $produit, SessionInterface $session, String $provenance): Response
    {
//        récupération du panier

        $panier = $session->get("panier", []);
        $id = $produit->getId();
        if(!empty($panier[$id])){
            if ($panier[$id] > 1 ){

                $panier[$id]--;
            }
            else
            {
                unset($panier[$id]);
            }
        }

//        on enregistre ça en session
        $session->set("panier", $panier);

        return $this->redirectToRoute($provenance);
    }

    /**
     * @Route("/delete/{id}/{provenance}", name="panier_delete")
     */
    public function supprimeProduit(Produit $produit, SessionInterface $session, String $provenance): Response
    {
//        récupération du panier

        $panier = $session->get("panier", []);
        $id = $produit->getId();
        if(!empty($panier[$id])){
            {
                unset($panier[$id]);
            }
        }

//        on enregistre ça en session
        $session->set("panier", $panier);

        return $this->redirectToRoute($provenance);
    }

    /**
     * @Route("/set/{id}/{provenance}", name="panier_set")
     */
    public function setProduit(Produit $produit, SessionInterface $session, Request $request, String $provenance): Response
    {
//        récupération du panier
        $panier = $session->get("panier", []);
        $id = $produit->getId();
        $panier[$id] = ($request->get('quantite'));
//      on enregistre ça en session
        $session->set("panier", $panier);

        return $this->redirectToRoute($provenance);
    }

}
