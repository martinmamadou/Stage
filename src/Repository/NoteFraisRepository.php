<?php

namespace App\Repository;

use DateTime;
use App\Entity\NoteFrais;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<NoteFrais>
 */
class NoteFraisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoteFrais::class);
    }

     /**
     * Récupérer tous les NoteFrais.
     *
     * @return NoteFrais[]
     */
    public function findAllNoteFrais(): array
    {
        return $this->findAll();
    }

    public function findNoteFraisForCurrentMonth(int $id): array
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

    public function findClientNoteFraisForCurrentMonth(): array
{
    $currentDate = new \DateTime();
    $firstDayOfMonth = (clone $currentDate)->modify('first day of this month')->setTime(0, 0, 0);
    $lastDayOfMonth = (clone $currentDate)->modify('last day of this month')->setTime(23, 59, 59);

    $qb = $this->createQueryBuilder('d')
        ->where('d.creation BETWEEN :start AND :end')
        ->andWhere('d.Carte_client = :carte_client')
        ->setParameter('start', $firstDayOfMonth)
        ->setParameter('end', $lastDayOfMonth)
        ->setParameter('carte_client', true);

    return $qb->getQuery()->getResult();
}

public function findNoteFraisByUser(int $id): array
{
    $qb = $this->createQueryBuilder('d')
        ->where('d.employe = :user')
        ->setParameter('user', $id);

    return $qb->getQuery()->getResult();
}
    //    /**
    //     * @return NoteFrais[] Returns an array of NoteFrais objects
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

    //    public function findOneBySomeField($value): ?NoteFrais
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
