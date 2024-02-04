<?php

namespace App\Transaction\Repository;

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
	
	public function getAllCurrentUserTransactionsQuery(UserInterface $user): Query
	{
		return $this->createQueryBuilder('transaction')
			->where('transaction.user = :user')
			->setParameter('user', $user)
			->getQuery();
	}
	
	public function getTransactionsApi(int $user): array
	{
		return $this->findBy(['user' => $user]);
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
	
	public
	function getTransactionsPerPeriod(?DateTimeInterface $dateStart, ?DateTimeInterface $dateEnd): Query
	{
		return $this->createQueryBuilder('transaction')
			->andWhere('transaction.date BETWEEN :startDate AND :endDate')
			->setParameter('startDate', $dateStart)
			->setParameter('endDate', $dateEnd)
			->getQuery();
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
}


