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
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Type;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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
	
	public function __construct(ManagerRegistry      $registry, protected CategoryRepository $categoryRepository,
								#[CurrentUser] ?User $user)
	{
		parent::__construct($registry, Transaction::class);
	}
	
	
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
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function getMaxAmount(int $user): float|bool|int|string|null
	{
		return $this->createQueryBuilder('transaction')
			->select('max(transaction.amount)')
			->andWhere('transaction.user = :user')
			->setParameter('user', $user)
			->getQuery()
			->getSingleScalarResult();
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function getTransactionSum(User $user, array $conditions = [], string $type = TransactionEnum::EXPENSE):
	float|bool|int|string|null
	{
		$queryBuilder = $this->createQueryBuilder('transaction')
			->select('SUM(transaction.amount)')
			->andWhere('transaction.user = :user')
			->setParameter('user', $user);
		
		foreach ($conditions as $key => $value) {
			if (in_array($key, ['date', 'category', 'user'])) {
				$queryBuilder->andWhere("transaction.$key = :$key")
					->setParameter($key, $value);
			}
		}
		
		if ($type) {
			$queryBuilder->andWhere('transaction.type = :type')
				->setParameter('type', $type);
		}
		
		return $queryBuilder->getQuery()
			->getSingleScalarResult() ?? 0;
	}
	
	private function matchConditions(array $conditions): string
	{
		return match (true) {
			isset($conditions['date']) => 'transaction.date = :date',
			isset($conditions['category']) => 'transaction.category = :category',
			isset($conditions['id']) => 'transaction.id = :id',
			default => '',
		};
	}
}


