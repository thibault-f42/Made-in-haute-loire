<?php


namespace App\Services;


use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\User\UserInterface;

class MotDePasseOublie
{

    public  function envoieMailReinitMotdePasse($email)
{
    file_put_contents('test.txt', $email) ;
}

}
