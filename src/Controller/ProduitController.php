<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\Produit1Type;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(Produit1Type::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="produit_show", methods={"GET"})
////     * @ParamConverter("id", class=App\Entity\Produit", options={"id": "id"})
//     */
//    public function show(Produit $produit): Response
//    {
//        return $this->render('produit/show.html.twig', [
//            'produit' => $produit,
//        ]);
//    }
//
    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(Produit1Type::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }

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
