<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Utilisateur;
use App\Repository\ConversationRepository;
use App\Services\MercureServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/chat", name="app_chat_")
 */
class ChatController extends AbstractController
{
    /**
     * @var Utilisateur
     */
    private $utilisateur;

    /**
     * @Route("/", name="index",methods={"GET"})
     */
    public function index(MercureServices $mercureServices): Response // todo temporaire
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /**
         * @var $utilisateur Utilisateur
         */
        $utilisateur = $this->getUser();

        $conversations = $utilisateur->getConversations()->getValues();
        /**
         * @var $conversation Conversation
         */

        $response = $this->render('chat/index.html.twig', [
            'conversations' => $conversations,
            'user' => $utilisateur
        ]);

        return $response;
    }
    /**
     * @Route("/{id}", name="Conversation")
     */
    public function conversation(int $id,
                                 ConversationRepository $conversationRepository): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $conversation = $conversationRepository->findOneBy(['id'=>$id]);
        $mesages = $conversation->getMessages()->toArray();

        /**
         * @var $utilisateur Utilisateur
         */
        $utilisateur = $this->getUser();
        /**
         * @var $conversatios Conversation
         */
        $conversations = $utilisateur->getConversations()->getValues();

        if ($conversation->getLastMessage() && $utilisateur !== $conversation->getLastMessage()->getUtilisateur()){
            $conversation->setNMessageNonVue(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conversation);
            $entityManager->flush();
        }

        return $this->render('chat/chat.twig', [
            'user' => $utilisateur,
            'conversations' => $conversations,
            'mesages' => $mesages,
            'conversationId' => $conversation->getId(),
        ]);
    }
}
