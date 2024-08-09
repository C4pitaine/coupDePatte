<?php

namespace App\Repository;

use App\Entity\Friandise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friandise>
 */
class FriandiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friandise::class);
    }

    /**
     * Permet de faire une recherche sur les noms ou sur l'animal d'une friandise
     *
     * @param string $search
     * @return array|null
     */
    public function search(string $search,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('f')
                    ->select('f as friandise','f.id,f.name,f.price,f.image,f.animal')
                    ->where('f.name LIKE :search OR f.animal LIKE :search')
                    ->setParameter('search','%'.$search.'%')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Friandise[] Returns an array of Friandise objects
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

    //    public function findOneBySomeField($value): ?Friandise
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
