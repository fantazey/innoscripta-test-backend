<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    /**
     * @param string $uid
     * @return Order|null
     */
    public function findOneByUID(string $uid): ?Order
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.uid = :uid')
            ->setParameter('uid', $uid)
            ->getQuery();
        try {
            $order = $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $order = null;
        }
        return $order;
    }
}
