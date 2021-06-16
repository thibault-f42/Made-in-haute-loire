<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use http\QueryString;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * recupere les sorties en fonction d'une recherche
     * @param SearchData $data
     * @return Produit[]
     */

    public function findSearch(SearchData $data) : array
    {

        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 'e', 's', 'v', 'c')
            ->join('p.entreprise' , 'e')
            ->join('p.sousCategorie', 's')
            ->join('e.ville', 'v')
            ->join('v.canton', 'c');


        if (!empty($data->categorie && empty($data->sousCategorie))) {

            $query = $query
                ->andWhere('p.categorie = :s')
                ->setParameter('s', $data->categorie->getId());
        }

        if (!empty($data->sousCategorie)) {
            $query = $query
                ->andWhere('p.sousCategorie = :s')
                ->setParameter('s', $data->sousCategorie->getId());
        }

        if (!empty($data->recherche)) {
            $query = $query
                ->andWhere('p.nomArticle LIKE :n')
                ->setParameter('n', "%{$data->recherche}%");
        }

        if (!empty($data->prixMin)) {
            $query = $query
                ->andWhere('p.prix >= :pmn')
                ->setParameter('pmn', $data->prixMin);
        }

        if (!empty($data->prixMax)) {
            $query = $query
                ->andWhere('p.prix <= :pmx')
                ->setParameter('pmx', $data->prixMin);
        }

        if (!empty($data->canton)) {

            $query = $query
                ->andWhere('p.entreprise  = e.id')
                ->andWhere('e.ville  = v.id')
                ->andWhere('v.canton =:cid' )
                ->setParameter('cid', $data->canton );
        }

        return $query->getQuery()->getResult();
    }
}
