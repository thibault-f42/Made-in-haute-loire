<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Entreprise;
use App\Entity\Fichier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Form\FiltreType;
use App\Form\Produit1Type;
use App\Form\ProduitType;
use App\Repository\EntrepriseRepository;
use App\Repository\FichierRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/{id}/edit", name="produit_detail", methods={"GET","POST"})
     */
    public function afficheDetailProduit(Request $request, Produit $produit): Response
    {
        return $this->render('produit/detailProduit.html.twig', [
            'produit' => $produit,
        ]);
    }



    /**
     * @Route("/{id}/Vitrine", name="produitPartenaire", methods={"GET"})
     */
    public function afficheProduitsPartenaire(UtilisateurRepository $utilisateurRepository, FichierRepository $fichierRepository, Entreprise $entreprise): Response
    {

        $produitsPartenaire =$entreprise->getProduits();
        $photosEntreprise= $fichierRepository->findBy(['typeFichier' => 'Photos_presentation_entreprise', 'entreprise'=>$entreprise]);


        return $this->render('produit/VitrinePartenaire.html.twig', [
            'produits' => $produitsPartenaire, 'entreprise'=> $entreprise, 'photosEntreprise'=>$photosEntreprise
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

            //On complète les informations sur le produit (etat vente, code produit, fichiers)
            if ($ajoutProduit->getStock() > 0 ) {
                $ajoutProduit->setEtatVente('Disponible');
            }
            else {$ajoutProduit->setEtatVente('Épuisé');
            }




                //On récupère les photos
                $images= $formAjoutProduit->get('photos')->getData();

                //On boucle pour récupérer toutes les images
                foreach ($images as $image) {

                    // On génère un nom unique
                    $nomFichier=md5(uniqid()).'.'.$image->guessExtension();

                    // On copie le fichier dans le dossier upload
                    $image->move(
                        $this->getParameter('images_produits_directory'), $nomFichier);

                    //On stocke le chemin d'accès en base de données
                    $fichier = new Fichier();
                    $fichier->setUrlFichier($nomFichier);
                    $fichier->setTypeFichier('Photos_presentation_entreprise');

                    //on ajoute le fichier a notre entreprise
                    $ajoutProduit->addFichier($fichier);

                }


            $codeproduit = " ";

            ;
            $ajoutProduit->setCodeProduit($codeproduit);
            //on ajoute le produit a notre entreprise
            $entreprise->addProduit($ajoutProduit);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ajoutProduit);
            $entityManager->flush();

            //creation du code produit après génération de l'id
            //todo checker le code produit de thibaut
            //42340      xx              yyy                 zzz    ttt
            //Idcommune  code zone géo   code Fournisseur    codcat  codesouscat

            $codeproduit = $this->getUser()->getEntreprise()->getVille()->getCodePostal().
                str_pad($this->getUser()->getEntreprise()->getVille()->getCanton()->getId(), 3, "0", STR_PAD_LEFT).
                str_pad($this->getUser()->getEntreprise()->getId(),3, "0", STR_PAD_LEFT).
                str_pad($ajoutProduit->getSousCategorie()->getCategorie()->getId(),3, "0", STR_PAD_LEFT).
                str_pad($ajoutProduit->getSousCategorie()->getId(),3, "0", STR_PAD_LEFT).
                str_pad($ajoutProduit->getId(),4, "0", STR_PAD_LEFT);
            $ajoutProduit->setCodeProduit($codeproduit);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ajoutProduit);
            $entityManager->flush();

            return $this->redirectToRoute('produitPartenaire');
        }

        return $this->render('produit/AjoutProduit.html.twig', [
            'produits' => $produitsPartenaire, 'ajoutProduitForm' => $formAjoutProduit->createView()
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

    /**
     * @Route("/{id}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit)
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }



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
     * @Route("/{id}/Modification", name="modifierProduit", methods={"GET","POST"})
     */
    public function modifier(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère les photos
            $images= $form->get('photos')->getData();

            //On boucle pour récupérer toutes les images
            foreach ($images as $image) {

                // On génère un nom unique
                $nomFichier=md5(uniqid()).'.'.$image->guessExtension();

                // On copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('images_produits_directory'), $nomFichier);

                //On stocke le chemin d'accès en base de données
                $fichier = new Fichier();
                $fichier->setUrlFichier($nomFichier);
                $fichier->setTypeFichier('Photos_produit');

                //on ajoute le fichier a notre entreprise
                $produit->addFichier($fichier);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($produit);
                $entityManager->flush();

            }

            return $this->redirectToRoute('produitPartenaire');
        }

        return $this->render('produit/modifierProduit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/Supprimer/image{id}", name = "supprimePhotoProduit", methods ="DELETE")
     */
    public function deletePhoto (Fichier $photo, Request $request)   {

        $donnees = json_decode($request->getContent(), true);
        // on vérifie la validité du token
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $donnees['_token'])) {
            $nom = $photo->getUrlFichier();
            //on supprime le fichier
            unlink($this->getParameter('images_produits_directory').'/'.$nom);

            //on supprime l'entrée de la base de donnée
            $em = $this->getDoctrine()->getManager();
            $em->remove($photo);
            $em->flush();

            //On répond en json
            return new JsonResponse(['success'=>1]);
        }
        else {
            //On répond en json
            return new JsonResponse(['error'=>'Erreur lors de la suppression', 400]);}
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit
        ]);
    }
}
