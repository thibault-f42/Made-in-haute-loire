<?php

namespace App\Services;


use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class MessageService
{
    public function envoi(string $content,
                          Utilisateur $utilisateur,
                          Conversation $conversation,
                          EntityManagerInterface $entityManager,
                          SerializerInterface $serializer,
                          MercureServices $mercureServices){

        $message = new Message();
        $message->setCorps($content);
        $message->setDate(new \DateTime());
        $message->setUtilisateur($utilisateur);
        $message->setConversarion($conversation);

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);
        $conversation->incrementNMessageNonVue();
        $entityManager->getConnection()->beginTransaction();
        try {
            $entityManager->persist($message);
            $entityManager->persist($conversation);
            $entityManager->flush();
            $entityManager->commit();
        }catch (\Exception $e){
            $entityManager->rollback();
            throw $e;
        }

        // Envoie au serveur mercure
        foreach ($conversation->getUser() as $userConv){
            if ($userConv !== $utilisateur){
                $route = ["http://localhost/Made-in-haute-loire/public/chat/{$message->getConversarion()->getId()}",
                    "http://localhost/Made-in-haute-loire/public/utilisateur/{$userConv->getid()}"
                ];
            }
        }
        $messageSerialized = $serializer->serialize($message,'json',[
            'attributes' => ['id', 'corps', 'date', 'conversation '=> ['id']]
        ]);
        $mercureServices->Post($messageSerialized,$route);
        return $message;
    }


}