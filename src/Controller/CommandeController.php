<?php

namespace App\Controller;

use App\Data\SearchCommande;
use App\Entity\Commande;
use App\Entity\Entreprise;
use App\Entity\Produit;
use App\Entity\SousCommande;
use App\Form\AdresseLivraisonType;
use App\Form\CommandeType;
use App\Form\EtatSousCommandeType;
use App\Form\FiltreCommandeType;
use App\Repository\CommandeRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\EtatCommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\SousCommandeRepository;
use App\Repository\UtilisateurRepository;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/validation/", name="commandeValidation")
     */
    public function validationCommande(Session $session, Request $request,
                                       ProduitRepository $produitRepository)
    {

        $panier = $session->get("panier", []);

        //on initialise le tableau de produits
        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $produitRepository->find($id);
            $dataPanier[] = ["produit" => $produit, "quantite" => $quantite];
            $total += $produit->getPrix() * $quantite;
        }


        if (isset($total) && $total != 0) {

            //On instancie Stripe
        \Stripe\Stripe::setApiKey('sk_test_51LiGzfIKG6NL7lD76dkjsaykpkzl5VnRW5UzH3r9PppxLgOmOnw6RKAUELQDxtL1hD1usdDSwa3KQvdETq69uTDn0096MNLLri');

            //on crée l'intention de paiment stripe
        $intent = \Stripe\PaymentIntent::create([
        'amount'=>$total*100,
        'currency'=>'eur',
        'payment_method' => 'pm_card_visa']);

       }
        else
        {
            $this->addFlash('danger','une erreur est survenue lors du paiement, annulation de la transaction');
            return $this->redirectToRoute('Accueil');
        }



       return $this->render('Security/Paiement.html.twig', ['intent'=>$intent]);
   }


    /**
     * @Route("/commandesBoard/", name="afficheSousCommandes")
     */
    public function commandePanneau(Request $request,
                                    Session $session,
                                    EntrepriseRepository $entrepriseRepository,
                                    EtatCommandeRepository $etatCommandeRepository,
                                    SousCommandeRepository $sousCommandeRepository): Response
    {
        $entreprise=   $entrepriseRepository->find($this->getUser()->getEntreprise()->getId());

        $data= new SearchCommande();
        $data->entreprise= $entreprise;
        $formSousCommande=  $this->createForm(FiltreCommandeType::class, $data);
        $etatsCommandes = $etatCommandeRepository->findAll();
        $formSousCommande->handleRequest($request);

        $sousCommandes = $entreprise->getSousCommandes();

        if ($formSousCommande->isSubmitted() && $formSousCommande->isValid())
        {
            $sousCommandes = $sousCommandeRepository->filtreSousCommande($data, $entreprise);
            return $this->render('commande/commandeBoard.html.twig', ['sousCommandes' => $sousCommandes ,'etatsCommandes'=>$etatsCommandes, 'form'=>$formSousCommande->createView()]);
        }



        return $this->render('commande/commandeBoard.html.twig', ['sousCommandes' => $sousCommandes ,'etatsCommandes'=>$etatsCommandes, 'form'=>$formSousCommande->createView()]);


    }

    /**
     * @Route("/changeEtat/", name="changeEtatSousCommande")
     */
    public function changeEtatSousCommande(Request $request, SousCommandeRepository $commandeRepository, EtatCommandeRepository $etatCommandeRepository)
    {

        $i=0;
        $sousCommandes[]= "";

        //on récupère un tableau de données pour l'état
        while ($request->get('select-'.$i))
        {
            $sousCommandes[$i]=['id'=>($request->get('sousCommande-'.$i)), 'etat'=>($request->get('select-'.$i))];
            $i++;
        }

        $entityManager =  $this->getDoctrine()->getManager();

        foreach ($sousCommandes as $entree)
        {
            $id = $entree['id'];
            $etat = $entree['etat'];

            $sousCommande = $commandeRepository->find($id);
            $etatCommande = $etatCommandeRepository->find($etat);
            $sousCommande->setEtat($etatCommande);
            $entityManager->persist($sousCommande);
            $entityManager->flush();
        }
        return $this->redirectToRoute('afficheSousCommandes');
    }


    /**
     * @Route("/commandes/", name="affichecommandesClient")
     */
    public function commandeClient(Request $request,
                                    Session $session,
                                    UtilisateurRepository $utilisateurRepository): Response
    {

        $utilisateur = $utilisateurRepository->find($this->getUser()->getId());

        $commandes = $utilisateur->getCommandes();

        return $this->render('commande/listeCommandesClient.html.twig', ['commandes'=>$commandes]);

    }


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

        if (empty($dataPanier)){
            $this->addFlash('danger', 'Votre panier est vide');
            return $this->redirectToRoute('panier_index');
        }

        return $this->render('commande/apperçuCommande.html.twig', ['dataPanier'=>$dataPanier, 'adresseForm'=>$form->createView()]);
    }

    /**
     * @Route("/CommandeValidee", name="generationCommande", methods={"GET","POST"})
     */
    public function generationCommande(Request $request, Session $session,
                                       ProduitRepository $produitRepository,
                                       EtatCommandeRepository
                                       $etatCommandeRepository):
    Response
    {
        $commande = new Commande();
        $panier= $session->get("panier", []);

        //on initialise le tableau de produits
        $dataPanier = [];
        $total = 0 ;
        foreach ($panier as $id => $quantite)
        {
            $produit = $produitRepository->find($id);

            $sousCommande = $this->creerSousCommande($produit, $quantite, $etatCommandeRepository);
            $commande->addSousCommande($sousCommande);
            $dataPanier[]= ["produit" => $produit, "quantite" => $quantite];
            $total  += $produit->getPrix() * $quantite;
        }


        if (empty($dataPanier)){
            $this->addFlash('danger', 'Votre panier est vide');
            return $this->redirectToRoute('panier_index');
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

    public function creerSousCommande(Produit $produit, int $quantite, EtatCommandeRepository $etatCommandeRepository) {
        $sousCommande = new SousCommande();
        $sousCommande->setProduit($produit);
        $sousCommande->setEntreprise($produit->getEntreprise());
        $sousCommande->setUtilisateur($this->getUser());
        $sousCommande->setQuantite($quantite);
        $etat = $etatCommandeRepository->findOneBy(['etat'=>'Commande validée']) ;
        $sousCommande->setEtat($etat);

        return $sousCommande;
    }
}
