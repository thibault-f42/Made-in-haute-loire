<?php


namespace App\Data;


use App\Entity\Canton;
use App\Entity\Categorie;
use App\Entity\Departement;
use App\Entity\SousCategorie;
use App\Entity\Ville;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{

    /**
     * @var Departement
     */

    public $departement;

    /**
     * @var Canton
     */

    public $canton;

    /**
     * @var Categorie
     */

    public $categorie;

    /**
     * @var SousCategorie
     */

    public $sousCategorie;


    /**
     * @var float
     */

    public $prixMin;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="prix", message="Le prix maximum doit être supérieur au prix minimum")
     * @var float
     */

    public $prixMax;

    /**
     * @var String
     */

    public $recherche = '';


    public function __construct()
    {
        $this->sousCategories = new ArrayCollection();
    }


    /**
     * @return Collection|SousCategorie[]
     */
    public function getSousCategories(): Collection
    {
        return $this->sousCategories;
    }


}
