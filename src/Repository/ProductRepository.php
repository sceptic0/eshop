<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Product::class);
        $this->connection = $connection;
    }

    public function findWhereIn(array $ids)
    {
        return $this->createQueryBuilder('p')
                ->andWhere('p.hash IN(:val)')
                ->setParameter('val', $ids)
                ->getQuery()
                ->getResult();

    }

    public function findWhereInArray(array $ids)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.image, p.price, p.title, p.hash')
            ->andWhere('p.hash IN(:val)')
            ->setParameter('val', $ids)
            ->getQuery()
            ->getResult();
    }

    public function filter($category, $attribute)
    {
        $query = $this->createQueryBuilder('p')
                ->join('p.attributeProduct', 'ap', 'WITH','ap.product = p.id');
            if ($attribute && is_array($attribute)) {
                foreach ($attribute as $type => $value) {
                    $query = $query->andWhere('ap.attributeOption IN (:val1)')
                        ->setParameter('val1', $attribute);
                }
            }

            if ($category) {
                $query = $query->andWhere('p.category = :val')
                    ->setParameter('val', $category);
            }

        return $query->getQuery()
                ->getResult();
    }
}
