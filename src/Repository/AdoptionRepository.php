<?php

namespace App\Repository;

use App\Entity\Adoption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adoption>
 */
class AdoptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adoption::class);
    }

    /**
     * Permet de faire une recherche sur les noms d'un animal avec un filtre sur son type
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function search(string $search,string $filtre,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('a')
                    ->select('a as adoption','a.id,a.name,a.type,a.genre,a.age,a.race,a.image')
                    ->where('a.name LIKE :search AND a.type LIKE :filtre')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre','%'.$filtre.'%')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Adoption[] Returns an array of Adoption objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Adoption
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
