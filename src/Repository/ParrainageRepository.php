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
    public function getAllParrainageFromUser(int $userId): ?array
    {
        return $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u')
                    ->innerJoin('p.animal', 'a')
                    ->where('u.id = :userId')
                    ->setParameter('userId', $userId)
                    ->getQuery()
                    ->getResult();
    }

     /**
     * Permet de trouver tout les parrainages d'un User
     *
     * @param integer $userId
     * @return array|null
     */
    public function getAllParrainageFromAnimal(int $animalId): ?array
    {
        return $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u')
                    ->innerJoin('p.animal', 'a')
                    ->where('a.id = :animalId')
                    ->setParameter('animalId', $animalId)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Permet de trouver tout les parrainages payé d'un User
     *
     * @param integer $userId
     * @return array|null
     */
    public function getParrainageFromUser(int $userId): ?array
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

    /**
     * Permet de trouver tout les parrainages payé d'un Animal
     *
     * @param integer $animalId
     * @return array|null
     */
    public function getParrainageFromAnimal(int $animalId): ?array
    {
        return $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u')
                    ->innerJoin('p.animal', 'a')
                    ->where('a.id = :animalId')
                    ->andWhere('p.status = :status')
                    ->setParameter('animalId', $animalId)
                    ->setParameter('status', 'payé')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Permet de faire une recherche les parrainages d'un user
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function search(string $search,string $filtre,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u')
                    ->innerJoin('p.animal', 'a')
                    ->where('(a.name LIKE :search) AND (a.type LIKE :filtre OR p.status LIKE :filtre)')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre','%'.$filtre.'%')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Permet de faire une recherche les parrainages d'un user
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function searchUser(string $search,string $filtre,string $user,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u')
                    ->innerJoin('p.animal', 'a')
                    ->where('(a.name LIKE :search) AND (a.type LIKE :filtre) AND (u.id = :user) AND (p.status = :status OR p.status = :statusAdopted)')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre','%'.$filtre.'%')
                    ->setParameter('status','payé')
                    ->setParameter('statusAdopted','stoppé car adopté')
                    ->setParameter('user',$user)
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
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
