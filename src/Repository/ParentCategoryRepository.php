<?php

namespace App\Repository;

use App\Entity\ParentCategory;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

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
    private ?User $user;
    private array $parentCategories;

    public function __construct(
        ManagerRegistry              $registry,
        protected CategoryRepository $categoryRepository,
        protected Security           $security)
    {
        parent::__construct($registry, ParentCategory::class);
        $this->user = $this->security->getUser();
        $this->parentCategories = $this->findBy(['user' => $this->user->getId()]);
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

    public function getAll(): array
    {
        return $this->findBy(['user' => $this->user->getId()]);
    }

    public function isSimilar(ParentCategory $category): void
    {
        if ($this->findBy(['name' => $category->getName()])) {
            throw new DuplicateKeyException('You have same category!');
        }
    }


}
