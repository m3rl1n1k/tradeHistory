<?php

namespace App\Transaction\Repository;

use App\Category\Repository\CategoryRepository;
use App\Entity\User;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionEnum;
use App\Transaction\Twig\TransactionExtension;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
	
	public function __construct(ManagerRegistry $registry, protected CategoryRepository $categoryRepository)
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
	
	public function getUserTransactionsQuery(UserInterface $user): Query
	{
		return $this->createQueryBuilder('transaction')
			->where('transaction.user = :user')
			->orderBy('transaction.id', 'DESC')
			->setParameter('user', $user)
			->getQuery();
	}
	
	public function getUserTransactions(int $user, array $orderBy = [], int $limit = null): array
	{
		return $this->findBy(['user' => $user], $orderBy, $limit);
	}
	
	/**
	 * @throws NonUniqueResultException
	 */
	public
	function getSumIncome(int $user): int
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
	public
	function getSumExpense(int $user): int
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
	
	public function getTransactionsPerPeriod(UserInterface $user, DateTimeInterface $dateStart, DateTimeInterface $dateEnd):
	string|array|int|float
	{
		return $this->createQueryBuilder('transaction')
			->andWhere('transaction.date BETWEEN :startDate AND :endDate')
			->andWhere('transaction.user = :user')
			->setParameter('startDate', $dateStart)
			->setParameter('endDate', $dateEnd)
			->setParameter('user', $user)
			->getQuery()
			->getResult();
	}
	
	protected function notFoundedTransaction($data): bool
	{
		if (empty($data)) {
			throw new NotFoundHttpException('Transaction not found!', null, Response::HTTP_NOT_FOUND);
		}
		return false;
	}
	
	public function getOneBy(int $id): ?object
	{
		$record = $this->find($id);
		$this->notFoundedTransaction($record);
		return $record;
	}
	
	public function updateDataTransaction(Transaction|null $transaction, mixed $update, User $user, $category_id): void
	{
		$category = $this->categoryRepository->findOneBy(['id' => $category_id]);
		
		$transaction->setAmount($update->getAmount());
		$transaction->setType($update->getType());
		$transaction->setDate($update->getDate());
		$transaction->setDescription($update->getDescription());
		$transaction->setUserId($user);
		$transaction->setCategory($category);
	}
}


