<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Enum\TransactionTypeEnum;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

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
    public function __construct(ManagerRegistry                    $registry,
                                protected ParentCategoryRepository $categoryRepository,
                                protected Security                 $security)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function calculateSum(array $transactions, array $options = []): float
    {
        $sum = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->getCategory() !== null && $options['category'] === $transaction->getCategory()->getId()) {
                $sum += $transaction->getAmount();
            }
        }
        return $sum;
    }

    public function getTotalExpenseByMonth(array $transactions): float
    {
        $sum = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->getType() === TransactionTypeEnum::Expense->value) {
                $sum += $transaction->getAmount();
            }
        }
        return $sum;
    }

    public function getTransactionsPerPeriod(DateTimeInterface $dateStart, DateTimeInterface $dateEnd): mixed
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.date BETWEEN :startDate AND :endDate')
            ->andWhere('t.user = :user')
            ->setParameter('startDate', $dateStart)
            ->setParameter('endDate', $dateEnd)
            ->setParameter('user', $this->security->getUser())
            ->getQuery()
            ->getResult();
    }

    /**
     * conditions ['date', 'category', 'user']
     *
     * @param string $type
     * @param array $conditions
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTransactionSum(array $conditions = [], string $type = TransactionTypeEnum::Expense->value): mixed
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('SUM(t.amount)')
            ->andWhere('t.user = :user')
            ->setParameter('user', $this->security->getUser());

        foreach ($conditions as $key => $value) {
            if (in_array($key, ['date', 'category', 'user'])) {
                $queryBuilder->andWhere("t.$key = :$key")
                    ->setParameter($key, $value);
            }
        }

        if ($type) {
            $queryBuilder->andWhere('t.type = :type')
                ->setParameter('type', $type);
        }

        return $queryBuilder->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getTransactionForCurrentMonth(): ?array
    {
        $list = [];
        $month = date("m");
        foreach ($this->getUserTransactions() as $transaction) {
            $transactionDate = $transaction->getDate();
            if ($month === $transactionDate->format('m'))
                $list[$transactionDate->format('d') . "." . $transaction->getId()] = $transaction;
        }
        return $list;
    }

    /**
     *
     * @param bool $rawQuery
     * @param int|null $max
     * @return Query|array
     */
    public function getUserTransactions(bool $rawQuery = false, ?int $max = null): Query|array
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->orderBy('t.date', 'DESC')
            ->setParameter('user', $this->security->getUser());
        if ($max !== null) {
            $query->setMaxResults(abs((int)$max));
        }
        return $rawQuery ? $query->getQuery() : $query->getQuery()->getResult();
    }

    public function getLastTransaction(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $this->security->getUser())
            ->orderBy('t.date', 'DESC')
            ->setMaxResults(10)
            ->getQuery()->getResult();
    }

    public function searchByCategory(string $category): array
    {
        return $this->findBy(['category' => $category, 'user' => $this->security->getUser()]);
    }
}


