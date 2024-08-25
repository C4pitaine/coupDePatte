<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Donation>
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donation::class);
    }

     /**
     * Permet de faire une recherche sur les noms / prénoms d"une personne ayant commandé des friandises
     *
     * @param string $search
     * @param string $filtre
     * @return array|null
     */
    public function search(string $search,string $filtre,?int $limit = null,?int $offset = 0): ?array
    {
        $search = htmlspecialchars($search);

        return $this->createQueryBuilder('d')
                    ->select('d as donation','d.id,d.lastName,d.firstName,d.email,d.montant,d.adresse,d.codePostal,d.ville,d.pays,d.status')
                    ->where('(d.lastName LIKE :search OR d.firstName LIKE :search) AND d.status LIKE :filtre')
                    ->setParameter('search','%'.$search.'%')
                    ->setParameter('filtre','%'.$filtre.'%')
                    ->setMaxResults($limit)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Donation[] Returns an array of Donation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Donation
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
