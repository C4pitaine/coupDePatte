<?php

namespace App\Repository;

use App\Entity\Parrainage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parrainage>
 */
class ParrainageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parrainage::class);
    }

    /**
     * Permet de trouver tout les parrainages d'un User
     *
     * @param integer $userId
     * @return array|null
     */
    public function getUser(int $userId): ?array
    {
        return $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u')
                    ->innerJoin('p.animal', 'a')
                    ->where('u.id = :userId')
                    ->andWhere('p.status = :status')
                    ->setParameter('userId', $userId)
                    ->setParameter('status', 'payé')
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Parrainage[] Returns an array of Parrainage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Parrainage
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
