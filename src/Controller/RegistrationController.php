<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             GuardAuthenticatorHandler $guardHandler,
                             LoginFormAuthenticator $authenticator,
                             VilleRepository $villeRepository,
                             \Swift_Mailer $mailer ): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //On génére un  toker d'activation
            $user->setActivationToken(md5(uniqid()));

            $user->setVendeur(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();




            // do anything else you need here, like send an email
            $message = (new \Swift_Message('Activation de votre compte'))
                ->setFrom('MadeInLHL@LoireHauteLoire.fr')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('Email/activation.html.twig', ['token'=>$user->getActivationToken()]), 'text/html'
                )
            ;

            $mailer->send($message);


            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        if ($request->get('ajax') && $request->get('registration_form')['codePostal']) {
            $codePostal = $request->get('registration_form')['codePostal'];

            $villes = $villeRepository->getVillesByCodePostalAjax($codePostal);

            return new JsonResponse([
                'content' => $this->renderView('registration/content/_selectVille.html.twig', compact('villes'))
            ]);
        }

        return $this->render('registration/inscription.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/activation/{token}" , name="activation")
     */
    public function activation(String $token, UtilisateurRepository $utilisateurRepository){

        $user = $utilisateurRepository->findOneBy(['activationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('cet utilisateur n\'existe pas');
        }
        else
        {
            $user->setIsVerified(1);
            $user->setActivationToken(null);
            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            //message flash
            $this->addFlash('message', 'compte activé avec succès');
        }


        return $this->redirectToRoute('Accueil');

    }
}



//
//
//    }


