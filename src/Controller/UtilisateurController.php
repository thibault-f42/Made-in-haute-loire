<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ModifUtilisateurType;
use App\Form\RegistrationFormType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use ContainerAq2fSIq\getRegistrationFormTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="utilisateurProfil", methods={"GET"})
     */
    public function afficheProfil(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/Modification-Compte", name="utilisateur_modification", methods={"GET","POST"})
     */
    public function modificationCompte(Request $request, Utilisateur $utilisateur, VilleRepository  $villeRepository): Response
    {

        $form = $this->createForm(ModifUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        if ($request->get('ajax') && $request->get('modif_utilisateur')['codePostal']) {
            $codePostal = $request->get('modif_utilisateur')['codePostal'];

            if ($codePostal === "tout") {
                $villes = $villeRepository->findAll();
            }
            else
            {

                $villes = $villeRepository->getVillesByCodePostalAjax($codePostal);
            }
            return new JsonResponse([
                'content' => $this->renderView('registration/content/_selectVille.html.twig', compact('villes'))
            ]);
        }

        return $this->render('utilisateur/modificationUtilisateur.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_delete", methods={"POST"})
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }


}
