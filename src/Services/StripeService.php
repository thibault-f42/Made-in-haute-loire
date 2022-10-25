<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\Produit;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeService
{
    private $privateKey;

    public function __construct()
    {
        if($_ENV['APP_ENV']  === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    /**
     * @param Produit $produit
     * @return \Stripe\PaymentIntent
     * @throws \Stripe\Exception\ApiErrorException
     */

    public function paymentIntent(Produit $produit)
    {
        \Stripe\Stripe::setApiKey($this->privateKey);

        return \Stripe\PaymentIntent::create([
            'amount' => $produit->getPrix() * 100,
            'currency' => Commande::DEVISE,
            'payment_method_types' => ['card']
        ]);
    }

    public function paiement(
        $amount,
        $currency,
        $description,
        array $stripeParameter
    )
    {
        \Stripe\Stripe::setApiKey($this->privateKey);
        $payment_intent = null;

        if(isset($stripeParameter['stripeIntentId'])) {
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }

        if($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            //TODO
        } else {
//            $payment_intent->cancel();
//            $this->addFlash('danger','une erreur est survenue lors du paiement, annulation de la transaction');
//            return $this->redirectToRoute('Accueil');
        }

        return $payment_intent;
    }

    /**
     * @param array $stripeParameter
     * @param Produit $produit
     * @return \Stripe\PaymentIntent|null
     */
    public function stripe(array $stripeParameter, Produit $produit)
    {
        return $this->paiement(
            $produit->getPrix() * 100,
            Commande::DEVISE,
            $produit->getNomArticle(),
            $stripeParameter
        );
    }

// todo pas fini
    public function controlePaiement (){
        if (false){

        }else{
            throw new Exception('Actuellement nous ne comprenons pas que le paiement est bien été envoyé : StripeService, controlePaiement');
        }
    }

}