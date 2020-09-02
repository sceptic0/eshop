<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findClientOrdersAssignedToAccount($id) {
        return $this->createQueryBuilder('o')
             ->join('App\Entity\User','u', 'WITH','o.user = u.id')
            ->andWhere('u.account = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }

    public function findOrdersWithAccountFilter($id)
    {
        return $this->createQueryBuilder('o')
            ->join('App\Entity\User', 'u', 'WITH', 'o.user = u.id')
            ->join('App\Entity\OrderProduct', 'op', 'WITH', 'o.id = op.order')
            ->andWhere('u.account = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }


}
