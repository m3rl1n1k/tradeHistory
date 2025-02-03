<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wallet;
use App\Trait\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    private ?User $user;

    public function __construct(ManagerRegistry    $registry,
                                protected Security $security)
    {
        parent::__construct($registry, Wallet::class);
        $this->user = $this->security->getUser();
    }

    use RepositoryTrait;

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

    public function getAll(): array
    {
        $result = [];
        foreach ($this->findBy(['user' => $this->user->getId()]) as $wallet) {
            $result[$wallet->getId()] = $wallet;
        }
        return $result;
    }
}
