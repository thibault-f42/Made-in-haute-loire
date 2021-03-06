<?php

namespace App\Repository;

use App\Entity\Canton;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Canton|null find($id, $lockMode = null, $lockVersion = null)
 * @method Canton|null findOneBy(array $criteria, array $orderBy = null)
 * @method Canton[]    findAll()
 * @method Canton[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CantonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Canton::class);
    }

    // /**
    //  * @return Canton[] Returns an array of Canton objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Canton
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
