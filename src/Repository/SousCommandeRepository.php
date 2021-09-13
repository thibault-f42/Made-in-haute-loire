<?php

namespace App\Repository;

use App\Data\SearchCommande;
use App\Entity\Entreprise;
use App\Entity\SousCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SousCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousCommande[]    findAll()
 * @method SousCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousCommande::class);
    }


    /**
     * recupere les sorties en fonction d'une recherche
     * @param SearchCommande $data
     * @return SousCommande[]
     */

    public function filtreSousCommande($data, Entreprise $entreprise){

        $query = $this
            ->createQueryBuilder('sc')
            ->select('sc','e', 'p', 'c')
            ->join('sc.entreprise' , 'e')
            ->join('sc.produit', 'p')
            ->join('sc.commande', 'c')
            ->andWhere('sc.entreprise = :ent')
            ->setParameter('ent', $entreprise->getId())
        ;

        if (!empty($data->etatCommande))
        {
            $query = $query
                ->andWhere('sc.etat = :et')
                ->setParameter('et', $data->etatCommande->getId());
        }

        if (!empty($data->dateMin))
        {
            $query = $query
                ->andWhere('c.dateCommande >= :dc')
                ->setParameter('dc', $data->dateMin);
        }

        if (!empty($data->dateMax))
        {
            $query = $query
                ->andWhere('c.dateCommande <= :dm')
                ->setParameter('dm', $data->dateMax);
        }

        if (!empty($data->produit))
        {
            $query = $query
                ->andWhere('p.id <= :pr')
                ->setParameter('pr', $data->produit);
        }

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return SousCommande[] Returns an array of SousCommande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SousCommande
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
