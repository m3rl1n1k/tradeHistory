<?php

namespace App\Transaction\Repository;

use App\Entity\User;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionEnum;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getAllUserCurrentTransactionsQuery(UserInterface|string $user): Query
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getSumIncome(int $user): int
    {
        $res = $this->createQueryBuilder('transaction')
            ->select('SUM(transaction.amount) as incomeSum')
            ->andWhere('transaction.user = :user')
            ->andWhere('transaction.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', TransactionEnum::INCOME)
            ->getQuery()
            ->getOneOrNullResult();
        return $res['incomeSum'] ?? 0;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getSumExpense(int $user): int
    {
        $res = $this->createQueryBuilder('transaction')
            ->select('SUM(transaction.amount) as expenseSum')
            ->andWhere('transaction.user = :user')
            ->andWhere('transaction.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', TransactionEnum::EXPENSE)
            ->getQuery()
            ->getOneOrNullResult();
        return $res['expenseSum'] ?? 0;

    }

    public function getTransactionsPerPeriod(?DateTimeInterface $dateStart, ?DateTimeInterface $dateEnd): Query
    {
        return $this->createQueryBuilder('transaction')
            ->andWhere('transaction.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $dateStart)
            ->setParameter('endDate', $dateEnd)
            ->getQuery();
    }
}


