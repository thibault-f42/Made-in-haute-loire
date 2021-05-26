<?php


namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Ville;
use App\Entity\Fichier;
use App\Entity\Utilisateur;
use App\Form\EntrepriseFormType;
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

class EntrepriseController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    /**
     * @Route ("/Devenir-Partenaire", name = "DevenirPartenaire")
     */
    public function AccueilPartenaire ()  {

        return $this->render('/Entreprise/Devenir-Partenaire.html.twig')  ;
    }

    /**
     * @Route ("/Inscription-Partenaire", name = "InscriptionFournisseur")
     */
    public function inscriptionPartenaire (Request $request,  EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, VilleRepository $villeRepository): Response
    {

        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseFormType::class, $entreprise);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());

//            On reucpère le user
            $entreprise->setUtilisateur($this->getUser());
            //On récupère les photos
            $images= $form->get('photos')->getData();
            //On boucle pour récupérer toutes les images
            foreach ($images as $image) {
                // On génère un nom unique
                $nomFichier=md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier upload
                $image->move(
                    $this->getParameter('images_entreprise_directory'), $nomFichier);
                //On stocke le chemin d'accès en base de données
                $fichier = new Fichier();
                $fichier->setUrlFichier($nomFichier);
                $fichier->setTypeFichier('Photos_Entreprise');
                //on ajoute le fichier a notre entreprise
                $entreprise->addFichier($fichier);

            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();


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




}
