<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Repository\UtilisateurRepository;
use App\Services\MercureServices;
use App\Services\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/messages", name="messages_")
 */
class MessageController extends AbstractController
{
    const ATTRIBUT_TO_SERIALIZE = ['id', 'corps', 'date','mine'];


    /**
     * @Route("/{id}", name="newMessage", methods={"POST"})
     * @return JsonResponse
     */
    public function newMessage(Request                $request,
                               Conversation           $conversation,
                               UtilisateurRepository  $utilisateurRepository,
                               MessageService $messageService)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $utilisateur = $utilisateurRepository->findOneBy(array('email' => $this->getUser()->getUsername()));
        $content = $request->get('mesage', "");
        $message = $messageService->envoi($content,$utilisateur,$conversation);


        return $this->json($message, Response::HTTP_CREATED, [],[
            'attributes' => self::ATTRIBUT_TO_SERIALIZE
        ]);
    }
}
