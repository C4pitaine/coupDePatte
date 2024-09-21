<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Animal>
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
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
                    ->select('a as animal','a.id,a.name,a.type,a.genre,a.age,a.race,a.adoptionDate,a.adopted,a.coverImage')
                    ->where('a.name LIKE :search AND a.type LIKE :filtre')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre','%'.$filtre.'%')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Permet de faire une recherche sur les noms d'un animal avec un filtre sur son type
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function type(string $search,string $filtre,string $type,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('a')
                    ->select('a as animal','a.id,a.name,a.type,a.genre,a.age,a.race,a.adoptionDate,a.adopted,a.coverImage')
                    ->where('a.name LIKE :search AND a.race = :filtre AND a.type = :type')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre',$filtre)
                    ->setParameter('type',$type)
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Permet d'envoyer toutes les races repris pour un type d'animal dans le formulaire de filtre
     *
     * @param string $type
     * @return array|null
     */
    public function findRaces(string $type): ?array
    {
        $type = htmlspecialchars($type);

        return $this->createQueryBuilder('a')
                    ->select('a.race')
                    ->where('a.type = :type')
                    ->setParameter('type', $type)
                    ->getQuery()
                    ->getScalarResult();
    }

    public function findAdopted(): ?array
    {
        return $this->createQueryBuilder('a')
                    ->select('a.name')
                    ->where('a.adopted = true')
                    ->getQuery()
                    ->getScalarResult();
    }

    //    /**
    //     * @return Animal[] Returns an array of Animal objects
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

    //    public function findOneBySomeField($value): ?Animal
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
