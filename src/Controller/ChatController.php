<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Utilisateur;
use App\Repository\ConversationRepository;
use App\Repository\UtilisateurRepository;
use App\Services\MercureServices;
use Doctrine\ORM\EntityManager;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Key\InMemory;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/chat", name="app_chat_")
 */
class ChatController extends AbstractController
{
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
         * @var $conversatios Conversation
         */

        $response = $this->render('chat/index.html.twig', [
            'conversations' => $conversations,
            'user' => $utilisateur
        ]);

        //todo utile ??
//        $response->headers->set('set-cookie',$mercureServices->CookieGenerator($utilisateur));
        return $response;
    }

    /**
     * @Route("/newConversation/{id}", name="newConversation", methods={"get"})
     */
    public function newConversation(
        int $id,
        Request $request,
        UtilisateurRepository $utilisateurRepository ): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $otherUser = $utilisateurRepository->findOneBy(array('id' => $id));
            if (!$utilisateurRepository->findOneBy(array('id' => $id))){ // L'utilitaire choisi n'existe pas
                throw new \Exception("L'utilisateur n'a pas été trouvé ");

            }
            $utilisateur = $utilisateurRepository->findOneBy(array('email' => $this->getUser()->getUsername()));
            if ($otherUser->getid() === $utilisateur->getId() ){ // Il est impossible de créer avec soi-même
                throw new \Exception("Vous ne pouvez pas créer de conversation avec vous-même ");
            }
        }catch (\Exception $e){
            $this->addFlash('warnig', $e->getMessage());

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }


        if ($utilisateur->findConversationByParticipants($otherUser)) {
            $id = $utilisateur->findConversationByParticipants($otherUser)->getId();
        }else{
            $conversation = new Conversation();
            $conversation->addUser($otherUser);
            $conversation->addUser($utilisateur);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conversation);
            $entityManager->flush();
            $id = $conversation->getId();
        }
        return $this->redirectToRoute('app_chat_Conversation', ['id' => $id]);
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
