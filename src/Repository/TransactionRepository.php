<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(ManagerRegistry          $registry,
                                protected Security       $security,
                                protected UserRepository $userRepository)
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
    public function saveAmount(EntityManagerInterface $entityManager, Transaction $transaction, bool $persist = false): void
    {
        if ($transaction->getType() === Transaction::INCOMING) {
            $this->incrementAmount($transaction);
        }
        if ($transaction->getType() === Transaction::EXPENSE) {
            $this->decrementAmount($transaction);
        }

        if ($persist)
            $entityManager->persist($transaction);
        $entityManager->flush();
    }

    protected function incrementAmount(Transaction $transaction): void
    {
        $transaction->setAmount($this->security->getUser()->getAmount() + $transaction->getAmount());
    }

    protected function decrementAmount(Transaction $transaction): void
    {
        $transaction->setAmount($this->security->getUser()->getAmount() - $transaction->getAmount());
    }
}
