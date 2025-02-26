<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wallet;
use App\Service\Interfaces\CrypticInterface;
use App\Trait\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Wallet>
 *
 * @method Wallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wallet[]    findAll()
 * @method Wallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WalletRepository extends ServiceEntityRepository
{
    use RepositoryTrait;

    private ?User $user;


    public function __construct(ManagerRegistry    $registry,
                                protected CrypticInterface $cryptic,)
    {
        parent::__construct($registry, Wallet::class);
    }


//    /**
//     * @return Wallet[] Returns an array of Wallet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Wallet
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

//    public function find($id, $lockMode = null, $lockVersion = null): ?object
//    {
//        $wallet = parent::find($id, $lockMode, $lockVersion);
//        if ($wallet) {
//            $wallet->setEncryptor($this->cryptic);  // Ensure Encryptor is set after retrieval
//        }
//        return $wallet;
//    }
//
//    public function findAll(): array
//    {
//        $wallets = parent::findAll();
//        foreach ($wallets as $wallet) {
//            $wallet->setEncryptor($this->cryptic); // Set Encryptor for all wallets
//        }
//        return $wallets;
//    }

}