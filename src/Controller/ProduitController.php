<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/mes-produit", name="produitPartenaire")
     * @return Response
     */
    public function afficheProduitsPartenaire(UtilisateurRepository $utilisateurRepository): Response
    {

        $utilisateur= $utilisateurRepository->find($this->getUser());
        $entreprise = $utilisateur->getEntreprise();
        $produitsPartenaire =$entreprise->getProduits();


        return $this->render('produit/ProduitPartenaire.twig', [
            'produits' => $produitsPartenaire,
        ]);
    }

    /**
     * @Route("/ajout-produit", name="AjoutProduit")
     * @return Response
     */
    public function ajoutProduit(UtilisateurRepository $utilisateurRepository, Request $request): Response
    {

        $utilisateur= $utilisateurRepository->find($this->getUser());
        $entreprise = $utilisateur->getEntreprise();
        $produitsPartenaire =$entreprise->getProduits();



        $ajoutProduit= new Produit();
        $formAjoutProduit = $this->createForm(ProduitType::class, $ajoutProduit);

        $formAjoutProduit->handleRequest($request);

        if ($formAjoutProduit->isSubmitted() && $formAjoutProduit->isValid()) {

                //on ajoute le fichier a notre entreprise
                $entreprise->addProduit($ajoutProduit);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entreprise);
                $entityManager->flush();

            }

            return $this->render('produit/AjoutProduit.html.twig', [
            'produits' => $produitsPartenaire, 'ajoutProduitForm' => $formAjoutProduit->createView()
        ]);
    }
}
