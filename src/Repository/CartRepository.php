<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * Permet de faire une recherche sur les noms / prénoms d"une personne ayant commandé des friandises
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function search(string $search,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('c')
                    ->select('c as cart','c.id,c.name,c.firstName,c.email,c.cart,c.total')
                    ->where('c.name LIKE :search OR c.firstName LIKE :search')
                    ->setParameter('search','%'.$search.'%')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Cart[] Returns an array of Cart objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cart
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
