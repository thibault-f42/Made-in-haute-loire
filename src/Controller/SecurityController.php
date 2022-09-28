<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\MotDePasseResetType;
use App\Repository\UtilisateurRepository;
use App\Services\MotDePasseOublie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route ("/Motdepasseoublie", name="motDePasseOublie")
     */
    public function reinitMotDePasse(

        MotDePasseOublie $motDePasseOublie,
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator )
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('Accueil');
        }
        $form=$this->createForm(MotDePasseResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();
            $utilisateur = $utilisateurRepository->findOneBy(['email'=> $donnees['email']]);

            if (!$utilisateur) {
                $this->addFlash('danger', "Cette adresse n'existe pas");
                $this->redirectToRoute('app_login');
            }

            $token= $tokenGenerator->generateToken();

            try { $utilisateur->setTokenMDP($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($utilisateur);
                $entityManager->flush();

            }catch (\Exception $e) {
                $this->addFlash('warning', "Une erreur est survenue" . $e->getMessage());
                return $this->redirectToRoute('app_login');
            }


            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('MadeInLHL@LoireHauteLoire.fr')
                ->setTo($utilisateur->getEmail())
                ->setBody($this->renderView('Email/reinitMotDePasse.html.twig', ['token'=>$token]), 'text/html'
                )
            ;
            $mailer->send($message);

            $this->addFlash('message', "Un mail vous a été envoyé");
            return $this->redirectToRoute('app_login');

        }


        return  $this->render('security/DemandeReinitMotDePasse.html.twig', ['emailForm'=>$form->createView() ] );

    }


    /**
     * @Route ("/reinitialisationMotDePasse/{token}", name="app_reset_password")
     */
    public function resetMotDePasse (
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder,
        String $token) {

        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['tokenMDP'=>$token]);

        if (!$utilisateur) {
            $this->addFlash('danger', "Cet utilisateur n'existe pas");
            $this->redirectToRoute('app_login');
        }

        if($request->isMethod('POST')){
            if ($request->request->get('motDePasse') ==  $request->request->get('motDePasseConfirm'))
            {
                $utilisateur->setTokenMDP(null);
                $utilisateur->setPassword($userPasswordEncoder->encodePassword($utilisateur, $request->request->get('motDePasse')));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                $this->addFlash('message', "Mot de passe modifié avec succès");

                return $this->redirectToRoute('app_login');
            }
            else
            {
                $this->addFlash('danger', "Les deux mots de passe ne correspondent pas");
                return $this->render('security/ReinitMotDePasse.html.twig', ['token'=>$token]);

            }
        }

        else
        {
            return $this->render('security/ReinitMotDePasse.html.twig', ['token'=>$token]);
        }
    }

}
