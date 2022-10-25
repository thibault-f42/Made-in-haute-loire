<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Repository\CommentairesRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\MessageRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use App\Services\FromAdd;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
/**
 * @Route("/signalement", name="signalement_")
 */
class SignalementController extends AbstractController
{
    /**
     * @Route("/", name="index" )
     */
    public function index(): Response
    {

        return $this->render('signalement/index.html.twig', [
            'controller_name' => 'SignalementController',
        ]);
    }
    /**
     * @Route("/new", name="new", methods={"POST"})
     */
    public function new(Request $request,
                        EntrepriseRepository $entrepriseRepository,
                        ProduitRepository $produitRepository,
                        UtilisateurRepository $utilisateurRepository,
                        CommentairesRepository $commentairesRepository,
                        MessageRepository $messageRepository,
                        EntityManagerInterface $entityManager,
                        FromAdd $fromAdd): RedirectResponse
    {
        $type = $request->get('type', "");
        $numero = $request->get('numero', "");
        $motif = $request->get('motif', "");
        if ($type != "" and $numero != ""){
            $signalement = new Signalement();
            switch ($type) {
                case 1: //Entreprise
                    $entreprise = $entrepriseRepository->find($numero);
                    $signalement->setEntreprise($entreprise);
                    break;
                case 2://Produit
                    $produit = $produitRepository->find($numero);
                    $signalement->setProduit($produit);
                    break;
                case 3://Utilisateur
                    $utilisateur = $utilisateurRepository->find($numero);
                    $signalement->setUtilisateur($utilisateur);
                    break;
                case 4://commentaire
                    $commentaire = $commentairesRepository->find($numero);
                    $signalement->setCommentaires($commentaire);
                    break;
                case 5://message
                    $message = $messageRepository->find($numero);
                    $signalement->setMessage($message);
                    break;
            }
            $signalement->setMotif($motif);
            $entityManager->getConnection()->beginTransaction();
//            try {
                $entityManager->persist($signalement);
                $entityManager->flush();
                $entityManager->commit();
                $this->addFlash('message', "Signalement envoyé avec succès ");
//            }catch (\Exception $e){
//                $entityManager->rollback();
//                dd($e);
//                $this->addFlash('warning', "Une erreur est survenue lors de l'enregistrement de votre signalement veuillez contacter le service technique ");
//            }
        }
        $last = $fromAdd->getLastPage($request);
        return new RedirectResponse($last);
    }
}
