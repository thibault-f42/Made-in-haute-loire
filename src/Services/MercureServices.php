<?php

namespace App\Services;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class MercureServices
{
    public function Post(Conversation $conversation,string $mesage ){
        define('JWT', $_ENV['MERCURE_JWT_SECRET']);
        $postData = http_build_query([
            'topic' => "http://localhost/Made-in-haute-loire/public/messages/{$conversation->getId()}",
            'data' => json_encode($mesage)
            ]);


        echo file_get_contents(
            'http://localhost:3000/.well-known/mercure',
            false,
            stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer ".JWT,
                    'content' => $postData,
                ]
            ])
        );



    }


}