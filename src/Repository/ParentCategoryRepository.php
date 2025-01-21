<?php

namespace App\Repository;

use App\Entity\ParentCategory;
use App\Entity\User;
use App\Trait\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<ParentCategory>
 *
 * @method ParentCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParentCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParentCategory[]    findAll()
 * @method ParentCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParentCategoryRepository extends ServiceEntityRepository
{
    use RepositoryTrait;

    private ?User $user;
    private array $parentCategories;

    public function __construct(
        ManagerRegistry              $registry,
        protected CategoryRepository $categoryRepository,
        protected Security           $security)
    {
        parent::__construct($registry, ParentCategory::class);
        $this->user = $this->security->getUser();
        if ($this->user !== null) {
            $this->parentCategories = $this->findBy(['user' => $this->user->getId()]);
        }
    }

    public function getMainAndSubCategories(): array
    {
        $categoryChoices = [];
        foreach ($this->parentCategories as $mainCategory) {
            $categoryChoices[$mainCategory->getName()] = [
                'parentCategory' => $mainCategory,
                'categories' => $mainCategory->getCategories()->toArray()
            ];
        }
        return $categoryChoices;
    }
}
