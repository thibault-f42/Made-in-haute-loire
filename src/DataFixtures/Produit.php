<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Entreprise;
use App\Entity\SousCategorie;

use Faker;

class Produit extends Fixture
{

    public function load(ObjectManager $manager)
    {


        Faker\Factory::create('fr_FR');

      // create 20 products! Bam!
            for ($i = 0; $i < 20; $i++)
             {
                $products[$i] = new Product();
                $products[$i]->setNomArticle('product '.$i);
                $products[$i]->setPrix(mt_rand(10, 100));
                $products[$i]->setStock(mt_rand(0, 100));
                if ($product->getStock > 0) {
                            $product->setEtatVente('Disponible');
                } else {
                            $product->setEtatVente('Non disponible');
                }
                $product->setCodeProduit('codeProduit');

                $entreprise = new Entreprise();
                $entreprise->setId(1);
                $product->setEntreprise($entreprise);
                $categorie = new sousCategorie;
                $categorie->setId(2);
                $product->setSousCategorie($categorie);

                $manager->persist($product);
            }

            $manager->flush();
        $factory =  Faker\Factory::create(10);
    }
}
