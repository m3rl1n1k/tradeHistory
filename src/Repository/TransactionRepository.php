<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Transaction\TransactionEnum;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class TransactionRepository extends ServiceEntityRepository
{

    private ?User $user;

    public function __construct(ManagerRegistry                    $registry,
                                protected ParentCategoryRepository $categoryRepository,
                                protected Security                 $security)
    {
        parent::__construct($registry, Transaction::class);
        $this->user = $this->security->getUser();
    }


    public function getUserTransactionsQuery(): Query
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->orderBy('t.date', 'DESC')
            ->setParameter('user', $this->user->getId())
            ->getQuery();
    }

    public function getTransactionsPerPeriod(DateTimeInterface $dateStart, DateTimeInterface $dateEnd): mixed
    {
        return $this->createQueryBuilder('transaction')
            ->andWhere('transaction.date BETWEEN :startDate AND :endDate')
            ->andWhere('transaction.user = :user')
            ->setParameter('startDate', $dateStart)
            ->setParameter('endDate', $dateEnd)
            ->setParameter('user', $this->user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTransactionSum(array $conditions = [], string $type = TransactionEnum::Expense->value): mixed
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('SUM(t.amount)')
            ->andWhere('t.user = :user')
            ->setParameter('user', $this->user);

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

    public function getAllPerMonth(): ?array
    {
        $list = [];
        $month = date("m");
        foreach ($this->getUserTransactionsResult() as $transaction) {
            $transactionDate = $transaction->getDate();
            if ($month === $transactionDate->format('m'))
                $list[$transactionDate->format('d') . "." . $transaction->getId()] = $transaction;
        }
        return $list;
    }

    public function getUserTransactionsResult()
    {
        if ($this->user === null) {
            return [];
        }
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->orderBy('t.date', 'DESC')
            ->setParameter('user', $this->user->getId())
            ->getQuery()
            ->getResult();
    }

    public function getLastUserTransactions(): array
    {
        return array_slice($this->getUserTransactionsResult(), 0, 10);
    }
}


