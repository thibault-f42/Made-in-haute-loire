<?php


namespace App\Data;



use App\Entity\EtatCommande;
use App\Entity\Produit;
use App\Entity\SousCategorie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class SearchCommande
{


    /**
     * @var EtatCommande
     */

    public $etatCommande;

    /**
     * @var Assert\Date
     */

      public $dateMin;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="dateMin", message="Cette date doit être supérieure à la date de départ")
     * @var Assert\Date
     */

    public $dateMax;

      /**
       * @var Produit
       */

       public $produit;

}
