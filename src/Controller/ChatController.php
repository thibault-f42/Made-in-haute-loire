<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Key\InMemory;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/chat", name="app_chat_")
 */
class ChatController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UtilisateurRepository $utilisateurRepository): \Symfony\Component\HttpFoundation\Response
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
}
