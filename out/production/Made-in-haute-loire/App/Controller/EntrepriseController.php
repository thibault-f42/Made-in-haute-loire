<?php


namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Ville;
use App\Entity\Fichier;
use App\Entity\Utilisateur;
use App\Form\EntrepriseFormType;
use App\Form\FichierType;
use App\Repository\FichierRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use function Symfony\Component\Translation\t;


class EntrepriseController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    /**
     * @Route ("/Partenaire", name = "Partenaire")
     */
    public function AccueilPartenaire (UtilisateurRepository $utilisateurRepository, FichierRepository $fichierRepository, Request $request)  {

        //préparation des données a afficher


        $utilisateur= $utilisateurRepository->find($this->getUser());
        $entreprise = $utilisateur->getEntreprise();

        //On récupère les fichiers de l'entreprise

        $fichiers = $fichierRepository->findBy(array('entreprise'=>$entreprise->getId()));


        return $this->render('Entreprise/Partenaire.html.twig', ['utilisateur'=>$utilisateur, 'entreprise'=>$entreprise, 'fichiers'=>$fichiers ])  ;
    }

    /**
     * @Route ("/Inscription-Partenaire", name = "InscriptionFournisseur")
     */
    public function inscriptionPartenaire (Request $request,  EntityManagerInterface $entityManager,VilleRepository $villeRepository): Response
    {

        $entreprise = new Entreprise();


        $form = $this->createForm(EntrepriseFormType::class, $entreprise);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            //On recupère le user
            $entreprise->setUtilisateur($this->getUser());

            //On récupère les photos
            $images= $form->get('photos')->getData();
            //On boucle pour récupérer toutes les images
            foreach ($images as $image) {
                // On génère un nom unique
                $nomFichier=md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('images_entreprises_directory'), $nomFichier);
                //On stocke le chemin d'accès en base de données
                $fichier = new Fichier();
                $fichier->setUrlFichier($nomFichier);
                $fichier->setTypeFichier('Photos_presentation_entreprise');
                //on ajoute le fichier a notre entreprise
                $entreprise->addFichier($fichier);
            }

            //On récupère les pieces justificatives
            $Kbis= $form->get('justificatifSiret')->getData();
            //On boucle pour récupérer toutes les images
            foreach ($Kbis as $image) {
                // On génère un nom unique
                $nomFichier=md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('images_kbis_directory'), $nomFichier);
                //On stocke le chemin d'accès en base de données
                $fichier = new Fichier();
                $fichier->setUrlFichier($nomFichier);
                $fichier->setTypeFichier('Document_Kbis_Entreprise');
                //on ajoute le fichier a notre entreprise
                $entreprise->addFichier($fichier);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute('Accueil');


        }

        if ($request->get('ajax') && $request->get('entreprise_form')['codePostal']) {
            $codePostal = $request->get('entreprise_form')['codePostal'];
            $villes = $villeRepository->getVillesByCodePostalAjax($codePostal);
            return new JsonResponse([
                'content' => $this->renderView('registration/content/_selectVille.html.twig', compact('villes'))
            ]);
        }

        return $this->render('/Entreprise/Formulaire-Inscription-Entreprise.html.twig', [
            'EntrepriseForm' => $form->createView(),
        ]);

    }

    /**
     * @Route ("/Supprimer/image{id}", name = "supprimePhotoEntreprise", methods ="DELETE")
     */
    public function deletePhoto (Fichier $photo, Request $request)   {

        $donnees = json_decode($request->getContent(), true);
        // on vérifie la validité du token
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $donnees['_token'])) {
            $nom = $photo->getUrlFichier();
            //on supprime le fichier
            if ($photo->getTypeFichier() == 'Photos_presentation_entreprise') {
                unlink($this->getParameter('images_entreprises_directory') . '/' . $nom);
            }
            if ($photo->getTypeFichier() == 'Document_Kbis_Entreprise') {
                unlink($this->getParameter('images_kbis_directory') . '/' . $nom);
            }

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
     * @Route("/{id}/Modification", name="modifierEntreprise", methods={"GET","POST"})
     */
    public function modifierEntreprise (Request $request, Entreprise $entreprise, VilleRepository $villeRepository)   {

        $form = $this->createForm(EntrepriseFormType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            //On recupère le user
            $entreprise->setUtilisateur($this->getUser());

            //On récupère les photos
            $images= $form->get('photos')->getData();
            //On boucle pour récupérer toutes les images
            foreach ($images as $image) {
                // On génère un nom unique
                $nomFichier=md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('images_entreprises_directory'), $nomFichier);
                //On stocke le chemin d'accès en base de données
                $fichier = new Fichier();
                $fichier->setUrlFichier($nomFichier);
                $fichier->setTypeFichier('Photos_presentation_entreprise');
                //on ajoute le fichier a notre entreprise
                $entreprise->addFichier($fichier);
            }

            //On récupère les pieces justificatives
            $Kbis= $form->get('justificatifSiret')->getData();
            //On boucle pour récupérer toutes les images
            foreach ($Kbis as $image) {
                // On génère un nom unique
                $nomFichier=md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('images_kbis_directory'), $nomFichier);
                //On stocke le chemin d'accès en base de données
                $fichier = new Fichier();
                $fichier->setUrlFichier($nomFichier);
                $fichier->setTypeFichier('Document_Kbis_Entreprise');
                //on ajoute le fichier a notre entreprise
                $entreprise->addFichier($fichier);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute('Partenaire');
        }

        if ($request->get('ajax') && $request->get('entreprise_form')['codePostal']) {
            $codePostal = $request->get('entreprise_form')['codePostal'];
            $villes = $villeRepository->getVillesByCodePostalAjax($codePostal);
            return new JsonResponse([
                'content' => $this->renderView('registration/content/_selectVille.html.twig', compact('villes'))
            ]);
        }

        return $this->render('Entreprise/modifierEntreprise.html.twig', [
            'entreprise' => $entreprise,
            'form'=> $form->createView()
        ]);
    }



}
