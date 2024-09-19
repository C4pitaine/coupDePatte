<?php

namespace App\Repository;

use App\Entity\Favori;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favori>
 */
class FavoriRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favori::class);
    }

    /**
     * Permet de trouver tout les favoris d'un User
     *
     * @param integer $userId
     * @return array|null
     */
    public function getUser(int $userId): ?array
    {
        return $this->createQueryBuilder('f')
                    ->innerJoin('f.user', 'u')
                    ->innerJoin('f.animal', 'a')
                    ->where('u.id = :userId')
                    ->setParameter('userId', $userId)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Permet de faire une recherche les favoris d'un user
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function searchUser(string $search,string $filtre,string $user,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('f')
                    ->innerJoin('f.user', 'u')
                    ->innerJoin('f.animal', 'a')
                    ->where('a.name LIKE :search AND a.type LIKE :filtre AND u.id = :user')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre','%'.$filtre.'%')
                    ->setParameter('user',$user)
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Favori[] Returns an array of Favori objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Favori
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
