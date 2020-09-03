<?php

namespace App\Repository;

use App\Entity\AttributeOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AttributeOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeOptions[]    findAll()
 * @method AttributeOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeOptions::class);
    }

    // /**
    //  * @return AttributeOptions[] Returns an array of AttributeOptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttributeOptions
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
