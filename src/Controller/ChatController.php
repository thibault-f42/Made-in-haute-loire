<?php

namespace App\Controller;

use App\Repository\ConversationRepository;
use App\Repository\UtilisateurRepository;
use App\Services\MercureServices;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Key\InMemory;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
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
    public function index(UtilisateurRepository $utilisateurRepository): \Symfony\Component\HttpFoundation\Response // todo temporaire
    {

        $username =  $this->getUser()->getUsername();
        $token = (new Builder(new JoseEncoder(),ChainedFormatter::default()))
            ->withClaim('mercure',['subscribe' => [sprintf("/%s",$username)]])
            ->getToken(
                new Sha256(),InMemory::plainText($this->getParameter('mercure_secrete_key'))
            );
        $response = $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
        ]);
        $response->headers->setCookie(
            new Cookie('mercureAuthorization',
                $token->toString(),
                (new \DateTime())->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',null, false,true,false,'strict'
            )
        );
        return $response;
    }
    /**
     * @Route("/ping", name="ping",methods={"POST"})
     */
    public function ping(MercureServices $mercureServices, ConversationRepository $conversationRepository){// todo temporaire

        $conversation = $conversationRepository->find(1);

        $route = [
            "http://localhost/Made-in-haute-loire/public/messages/"
        ];
        $mercureServices->Post($conversation,"test de mesage",$route);

        return $this->redirectToRoute('app_chat_index');
    }
}
