<?php

namespace App\Controller;

use App\Entity\AdresseLivraison;
use App\Entity\Commande;
use App\Entity\Produit;
use App\Form\AdresseLivraisonType;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

/**
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/résumé/", name="commandeApperçu")
     */
    public function apperçuCommande(Request $request, Session $session, ProduitRepository $produitRepository): Response
    {


        $panier= $session->get("panier", []);

        //on initialise le tableau de produits
        $dataPanier = [];
        $total = 0 ;

        $form = $this->createForm(AdresseLivraisonType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $adresseLivraison=$form->getData();
            $adresseLivraison->addUtilisateur($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adresseLivraison);
            $entityManager->flush();

            return $this->redirectToRoute('commandeApperçu');

        }
        foreach ($panier as $id => $quantite)
        {
            $produit = $produitRepository->find($id);
            $dataPanier[]= ["produit" => $produit, "quantite" => $quantite];

            $total  += $produit->getPrix() * $quantite;
        }

        return $this->render('commande/apperçuCommande.html.twig', ['dataPanier'=>$dataPanier, 'adresseForm'=>$form->createView()]);
    }

    /**
     * @Route("/CommandeValidee", name="generationCommande", methods={"GET","POST"})
     */
    public function generationCommande(Request $request, Session $session, ProduitRepository $produitRepository): Response
    {
        $commande = new Commande();


        $panier= $session->get("panier", []);

        //on initialise le tableau de produits
        $dataPanier = [];
        $total = 0 ;
        foreach ($panier as $id => $quantite)
        {
            $produit = $produitRepository->find($id);
            $commande->addProduit($produit);
            $dataPanier[]= ["produit" => $produit, "quantite" => $quantite];
            $total  += $produit->getPrix() * $quantite;
        }

        $commande->setPrix($total);
        $jourCommande = date_create();
        //création d'une deuxieme variable pour le délai de livraison
        $jour = date_create();


        $commande->setDateCommande($jourCommande);

        $delaiLivraison = new \DateInterval('P14D');
        $jourLivraison = $jour->add($delaiLivraison);

        $commande->setDateLivraison($jourLivraison);

        $codeCommande  = $this->creerCodeCommande();
        $commande->setCodeCommande($codeCommande);
        $utilisateur = $this->getUser();
        $commande->setUtilisateur($utilisateur);
        $commande->setAdresseLivraison($utilisateur->getAdresseLivraison());

        $commande->setDescriptif('Commande passée avec succès');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('message', "Commande validée avec succès");

        return $this->redirectToRoute('Accueil');

    }
    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="commande_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('commande_index');
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commande_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Commande $commande): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commande_index');
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_delete", methods={"POST"})
     */
    public function delete(Request $request, Commande $commande): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_index');
    }


    public function  creerCodeCommande ()
    {
        return "";
    }
}
