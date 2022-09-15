<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Fichier;
use App\Entity\Utilisateur;
use App\Form\EntrepriseFormType;
use App\Form\EntrepriseType;
use App\Form\FichierType;
use App\Repository\EntrepriseRepository;
use App\Repository\FichierRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use App\Services\FromAdd;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/entreprise")
 */
class EntrepriseController extends AbstractController
{

    /**
     * @Route ("/Partenaire", name = "Partenaire")
     */
    public function AccueilPartenaire (UtilisateurRepository $utilisateurRepository,
                                       FichierRepository $fichierRepository,
                                       Request $request)  {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $utilisateur= $utilisateurRepository->find($this->getUser());
        if (!$utilisateur->getVendeur()){
            return $this->redirectToRoute('InscriptionFournisseur');
        }
        //préparation des données a afficher


        $utilisateur= $utilisateurRepository->find($this->getUser());
        $entreprise = $utilisateur->getEntreprise();

        //On récupère les fichiers de l'entreprise

        $fichiers = $fichierRepository->findBy(array('entreprise'=>$entreprise->getId()));

        $ajoutphoto = new Fichier();
        $formAjoutPhoto = $this->createForm(FichierType::class, $ajoutphoto);

        $formAjoutPhoto->handleRequest($request);

        if ($formAjoutPhoto->isSubmitted() && $formAjoutPhoto->isValid()) {

            //On recupère le user
            $entreprise->setUtilisateur($utilisateurRepository->findOneBy(array('email' => $this->getUser()->getUsername())));

            //On récupère les photos
            $images= $formAjoutPhoto->get('fichier')->getData();

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

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entreprise);
                $entityManager->flush();

            }

            return $this->redirectToRoute('Partenaire');
        }

        return $this->render('Entreprise/Partenaire.html.twig', ['utilisateur'=>$utilisateur, 'entreprise'=>$entreprise, 'fichiers'=>$fichiers, 'photoAjoutForm' => $formAjoutPhoto->createView() ])  ;
    }

    /**
     * @Route ("/Inscription-Partenaire", name = "InscriptionFournisseur")
     */
    public function inscriptionPartenaire (Request $request,
                                           EntityManagerInterface $entityManager,
                                           VilleRepository $villeRepository,
                                           UtilisateurRepository $utilisateurRepository,
                                           FromAdd $fromAdd
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseFormType::class, $entreprise);
        $form->handleRequest($request);

        $last = $fromAdd->getLastPage($request);


        if ($form->isSubmitted() && $form->isValid()) {

            //On recupère le user
            $utilisateur = $utilisateurRepository->findOneBy(array('email' => $this->getUser()->getUsername()));
            $entreprise->setUtilisateur($utilisateur);

            //On récupère les photos
            $fromAdd->savePicture($form->get('photos')->getData(),
                $entreprise,
                $this->getParameter('images_entreprises_directory'),
                'Photos_presentation_entreprise');

            //On récupère les pieces justificatives
            $fromAdd->savePicture($form->get('justificatifSiret')->getData(),
                $entreprise,
                $this->getParameter('images_kbis_directory'),
                'Document_Kbis_Entreprise');

            //On récupère les pieces justificatives
            $fromAdd->savePicture($form->get('carteIdentite')->getData(),
                $entreprise,
                $this->getParameter('images_entreprises_carteID_directory'),
                'Document_carte_ID_Entreprise');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();

            return $this->redirectToRoute($last);
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
            unlink($this->getParameter('images_entreprises_directory').'/'.$nom);

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
    public function modifierEntreprise (Request $request,
                                        Entreprise $entreprise,
                                        VilleRepository $villeRepository,
                                        UtilisateurRepository $utilisateurRepository,
                                        FromAdd $fromAdd)   {
        //todo Tout le monde a accès à ça
        $form = $this->createForm(EntrepriseFormType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            //On recupère le user
            $utilisateur = $utilisateurRepository->findOneBy(array('email' => $this->getUser()->getUsername()));
            $entreprise->setUtilisateur($utilisateur);

            //On récupère les photos
            $fromAdd->savePicture($form->get('photos')->getData(),
                $entreprise,
                $this->getParameter('images_entreprises_directory'),
                'Photos_presentation_entreprise');

            //On récupère les pieces justificatives
            $fromAdd->savePicture($form->get('justificatifSiret')->getData(),
                $entreprise,
                $this->getParameter('images_kbis_directory'),
                'Document_Kbis_Entreprise');

            //On récupère les pieces justificatives
            $fromAdd->savePicture($form->get('carteIdentite')->getData(),
                $entreprise,
                $this->getParameter('images_entreprises_carteID_directory'),
                'Document_carte_ID_Entreprise');

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
    /**
     * @Route("/", name="entreprise_index", methods={"GET"})
     */
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entrepriseRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="entreprise_show", methods={"GET"})
     */
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="entreprise_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Entreprise $entreprise): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entreprise_index');
        }

        return $this->render('entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entreprise_delete", methods={"POST"})
     */
    public function delete(Request $request, Entreprise $entreprise): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($entreprise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('entreprise_index');
    }
}
