<?php

namespace App\Services;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class MercureServices
{
    public function Post(string $mesage ,array $targets = []){
        define('JWT', $_ENV['MERCURE_JWT_SECRET']);

        foreach ($targets as $cible ){
            $postData = http_build_query([
                'topic' => $cible,
                'data' => json_encode($mesage)

            ]);
            dump($cible);
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
dd('fin');
    }


}