<?php

namespace App\Repository;

use App\Entity\Budget;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Budget>
 *
 * @method Budget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Budget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Budget[]    findAll()
 * @method Budget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetRepository extends ServiceEntityRepository
{
    private ?User $user;

    public function __construct(ManagerRegistry    $registry, protected Security $security)
    {
        parent::__construct($registry, Budget::class);
        $this->user = $this->security->getUser();
    }
    public function getAll(): array
    {
        return $this->findAll();
//        $records = $this->findBy(['user' =>  $this->user->getId()]);
//        return $records ?? [];
    }
}
