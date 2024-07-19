<?php

namespace App\Repository;

use DateTime;
use App\Entity\Devis;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Devis>
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

     /**
     * Récupérer tous les devis.
     *
     * @return Devis[]
     */
    public function findAllDevis(): array
    {
        return $this->findAll();
    }

    public function findDevisForCurrentMonth(int $id): array
    {
        $currentDate = new \DateTime();
        $firstDayOfMonth = (clone $currentDate)->modify('first day of this month')->setTime(0, 0, 0);
        $lastDayOfMonth = (clone $currentDate)->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('d')
            ->where('d.employe = :user')
            ->andWhere('d.createdAt BETWEEN :start AND :end')
            ->andWhere('d.Carte_client = :carte_client')
            ->setParameter('user', $id)
            ->setParameter('start', $firstDayOfMonth)
            ->setParameter('end', $lastDayOfMonth)
            ->setParameter('carte_client', false);

        return $qb->getQuery()->getResult();
    }

    public function findClientDevisForCurrentMonth(int $id): array
{
    $currentDate = new \DateTime();
    $firstDayOfMonth = (clone $currentDate)->modify('first day of this month')->setTime(0, 0, 0);
    $lastDayOfMonth = (clone $currentDate)->modify('last day of this month')->setTime(23, 59, 59);

    $qb = $this->createQueryBuilder('d')
        ->where('d.client = :client')
        ->andWhere('d.creation BETWEEN :start AND :end')
        ->andWhere('d.Carte_client = :carte_client')
        ->setParameter('client', $id)
        ->setParameter('start', $firstDayOfMonth)
        ->setParameter('end', $lastDayOfMonth)
        ->setParameter('carte_client', true);

    return $qb->getQuery()->getResult();
}

public function findDevisByUser(int $id): array
{
    $qb = $this->createQueryBuilder('d')
        ->where('d.employe = :user')
        ->setParameter('user', $id);

    return $qb->getQuery()->getResult();
}
    //    /**
    //     * @return Devis[] Returns an array of Devis objects
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

    //    public function findOneBySomeField($value): ?Devis
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
