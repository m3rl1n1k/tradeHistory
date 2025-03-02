<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Enum\TransactionTypeEnum;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getTransactionsPerPeriodByCategory(Category $category, DateTimeInterface $startDate, DateTimeInterface $endDate): mixed
    {
        $builder = $this->createQueryBuilder('t');
//        $builder->select('t.amount');
        $builder->where('t.category = :category')
            ->setParameter('category', $category);
        $builder->andWhere('t.date BETWEEN :startDate AND :endDate');
        $result = $builder->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();

        return $result;
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