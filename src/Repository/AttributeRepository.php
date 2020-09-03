<?php

namespace App\Repository;

use App\Entity\Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Atribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Atribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Atribute[]    findAll()
 * @method Atribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    // /**
    //  * @return Atribute[] Returns an array of Atribute objects
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
    public function findOneBySomeField($value): ?Atribute
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
