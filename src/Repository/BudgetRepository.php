<?php

namespace App\Repository;

use App\Entity\Budget;
use App\Entity\Transaction;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Budget>
 */
class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    //    /**
    //     * @return Budget[] Returns an array of Budget objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Budget
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getBudgetSummary(int $userId, string $month): array
    {
        $lastDayOfMonth = new DateTime("last day of $month 23:59:59");
        $startDayOfMonth = new DateTime($month . "00:00:00");

        return $this->createQueryBuilder('b')
            ->select(
                'IDENTITY(b.category) AS categoryId',
                'SUM(b.plannedAmount) AS planned',
                'COALESCE(t.amount) AS actual',
                '(SUM(b.plannedAmount) - COALESCE(SUM(t.amount), 0)) AS remaining'
            )
            ->leftJoin(Transaction::class, 't', 'WITH',
                'b.category = t.category 
        AND b.user = t.user AND t.date BETWEEN b.month AND :lastDayOfMonth')
            ->where('b.user = :userId')
            ->andWhere('b.month = :month')
            ->setParameter('userId', $userId)
            ->setParameter('month', $startDayOfMonth)
            ->setParameter('lastDayOfMonth', $lastDayOfMonth)
            ->groupBy('b.category')
            ->getQuery()
            ->getResult();
    }
}