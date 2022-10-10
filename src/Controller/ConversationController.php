<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Conversation;
use App\Entity\Produit;
use App\Entity\SousCommande;
use App\Entity\Utilisateur;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\SousCommandeRepository;
use App\Repository\UtilisateurRepository;
use App\Services\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conversation", name="conversation_")
 */
class ConversationController extends AbstractController
{
    /**
     * @var UtilisateurRepository
     */
    private $utilisateurRepository;
    /**
     * @var MessageService
     */
    private $messageService;
    /**
     * @var ProduitRepository
     */
    private $produitRepository;
    /**
     * @var Utilisateur
     */
    private $utilisateur;
    /**
     * @var SousCommandeRepository
     */
    private $sousCommandeRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository,
                                MessageService $messageService,
                                ProduitRepository $produitRepository,
                                SousCommandeRepository $sousCommandeRepository
                               )
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->messageService = $messageService;
        $this->produitRepository = $produitRepository;
        $this->sousCommandeRepository = $sousCommandeRepository;
    }

    /**
     * @Route("/newConversation/{id}", name="newConversation", methods={"get"})
     */
    public function newConversation(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->utilisateur = $this->getUser();
        try {
            $otherUser = $this->utilisateurRepository->findOneBy(array('id' => $id));
            if (! $this->utilisateurRepository->findOneBy(array('id' => $id))){ // L'utilitaire choisi n'existe pas
                throw new \Exception("L'utilisateur n'a pas été trouvé ");

            }
            if ($otherUser->getid() === $this->utilisateur->getId() ){ // Il est impossible de créer avec soi-même
                throw new \Exception("Vous ne pouvez pas créer de conversation avec vous-même ");
            }
        }catch (\Exception $e){
            $this->addFlash('warnig', $e->getMessage());

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        /**
         * @var $conversation Conversation
         */
        if ($this->utilisateur->findConversationByParticipants($otherUser)) {
            foreach ($this->utilisateur->findConversationByParticipants($otherUser) as $conversation){
                if (!$conversation->getProduit()){
                    $conversationNumber = $conversation->getId();
                }
            }
        }
        if ($conversationNumber == null){
            $conversationNumber = $this->messageService->newConversation([$otherUser ,$this->utilisateur ], true);
        }
        return $this->redirectToRoute('app_chat_Conversation', ['id' => $conversationNumber]);
    }

    /**
     * @Route("/newConversationByProduit/{id}", name="newConversationByProduit", methods={"get"})
     */
    public function newConversationByProduit(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->utilisateur = $this->getUser();
        try {
            $produit = $this->produitRepository->findOneBy(array('id'=>$id));
            if (!$produit){
                throw new \Exception("Le produit n'existe pas.");
            }
            if (!$produit->getActiveChat()){
                throw new \Exception("Le chat n'est pas activé sur ce produit.");
            }
            if ($produit->getEntreprise()->getUtilisateur() === $this->utilisateur ){
                throw new \Exception("Vous ne pouvez pas créer de conversation avec vous-même ");
            }
        }catch (\Exception $e){
            $this->addFlash('warnig', $e->getMessage());

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $utilisateurs = [$this->utilisateur, $produit->getEntreprise()->getUtilisateur()];
        $conversation = $this->messageService->newConversation($utilisateurs, false);
        $conversation->addProduit($produit);
        $conversation->setNom($produit->getNomArticle());
        $conversationNumber = $this->messageService->save($conversation);

        return $this->redirectToRoute('app_chat_Conversation', ['id' => $conversationNumber]);
    }

    /**
     * @Route("/newConversationBySousCommande/{numCommande}/{numProduit}", name="newConversationBySousCommande", methods={"get"})
     */
    public function newConversationBySousCommande(int $numCommande, int $numProduit): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->utilisateur = $this->getUser();
        try {

            /**
             * @var $produit Produit
             */
            /**
             * @var $commande SousCommande
             */
            $produit = $this->produitRepository->findOneBy(array('id'=>$numProduit));
            $commande = $this->sousCommandeRepository->findOneBy(array('id'=>$numCommande));

            if (!$produit){
                throw new \Exception("Le produit n'existe pas.");
            }
            if (!$commande){
                throw new \Exception("La commande n'existe pas.");
            }

            if ($produit->getEntreprise()->getUtilisateur() === $this->utilisateur ){
                throw new \Exception("Vous ne pouvez pas créer de conversation avec vous-même ");
            }
        }catch (\Exception $e){
            $this->addFlash('warnig', $e->getMessage());

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        /**
         * @var $conversation Conversation
         */
        if ($this->utilisateur->findConversationByParticipants($produit->getEntreprise()->getUtilisateur())){
            foreach ($this->utilisateur->findConversationByParticipants($produit->getEntreprise()->getUtilisateur()) as $conversation) {
                if ($conversation->getProduit() === $produit AND $conversation->getSousCommande() === $commande ) {
                    $conversationNumber = $conversation->getId();
                }
            }
        }
        if (!isset($conversationNumber)){
            $utilisateurs = [$this->utilisateur, $produit->getEntreprise()->getUtilisateur()];
            $conversation = $this->messageService->newConversation($utilisateurs, false);
            $conversation->addProduit($produit);
            $conversation->addSousCommande($commande);
            $conversation->setNom("litige {$produit->getNomArticle()}");
            $conversationNumber = $this->messageService->save($conversation);
        }
        return $this->redirectToRoute('app_chat_Conversation', ['id' => $conversationNumber]);
    }
}
