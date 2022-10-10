<?php

namespace App\Services;


use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;


class MessageService
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var MercureServices
     */
    private $mercureServices;

    public function __construct(EntityManagerInterface $entityManager,
                                SerializerInterface $serializer,
                                MercureServices $mercureServices)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->mercureServices = $mercureServices;
    }


    public function envoi(string $content,
                          Utilisateur $utilisateur,
                          Conversation $conversation){

        $message = new Message();
        $message->setCorps($content);
        $message->setDate(new \DateTime());
        $message->setUtilisateur($utilisateur);
        $message->setConversarion($conversation);

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);
        $conversation->incrementNMessageNonVue();
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();
            $this->entityManager->commit();
        }catch (\Exception $e){
            $this->entityManager->rollback();
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
        $messageSerialized = $this->serializer->serialize($message,'json',[
            'attributes' => ['id', 'corps', 'date', 'conversation '=> ['id']]
        ]);
        $this->mercureServices->Post($messageSerialized,$route);
        return $message;
    }

    public function newConversation(array $utilisateurs,bool $save)
    {
        $conversation = new Conversation();

        foreach ($utilisateurs as $utilisateur){
            $conversation->addUser($utilisateur);
        }
        $conversation->setNMessageNonVue(0);
        if ($save){
            return $this->save($conversation);
        }
        else{
            return $conversation;
        }
    }

    public function save(Conversation $conversation)
    {
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();
        $id = $conversation->getId();
        return $id;
    }


}