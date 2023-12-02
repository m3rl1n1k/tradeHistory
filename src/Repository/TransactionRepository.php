<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionEnum;
use App\Service\SerializerService;
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

    public function __construct(ManagerRegistry             $registry,
                                protected SerializerService $serializerService)
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

    public function getAllUserCurrentTransactionsQuery(UserInterface $user): Query
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }

    public function getAllInString(int $user): string
    {
        /** @var User $user */
        $list = $this->findBy(
            [
                'user' => $user
            ]);
        $list = trim($this->serializerService->serializeArray($list), '[]');
        $list = str_replace('{', '[', $list);
        $list = str_replace(':', '=>', $list);
        return str_replace('}', ']', $list);
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
        return $res['incomeSum'];
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
        return $res['expenseSum'];

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


