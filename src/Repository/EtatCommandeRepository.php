<?php

namespace App\Repository;

use App\Entity\EtatCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EtatCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatCommande[]    findAll()
 * @method EtatCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatCommande::class);
    }

    // /**
    //  * @return EtatCommande[] Returns an array of EtatCommande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EtatCommande
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
