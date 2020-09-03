<?php

namespace App\Repository;

use App\Entity\AttributeProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AttributeProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeProduct[]    findAll()
 * @method AttributeProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeProduct::class);
    }

    // /**
    //  * @return AttributeProduct[] Returns an array of AttributeProduct objects
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
    public function findOneBySomeField($value): ?AttributeProduct
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
