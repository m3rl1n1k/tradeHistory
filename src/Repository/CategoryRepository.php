<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
	private ?User $user;
	
	public function __construct(
		ManagerRegistry                 $registry,
		protected SubCategoryRepository $subCategoryRepository,
		protected Security              $security)
	{
		parent::__construct($registry, Category::class);
		$this->user = $this->security->getUser();
	}
	
	public function getAll(): array
	{
		return $this->findBy(['user' => $this->user->getId()]);
	}
	
	public function getMainAndSubCategories(): array
	{
		/** @var Category $mainCategory */
		$mainCategories = $this->getAll();
		$categoryChoices = [];
		
		foreach ($mainCategories as $mainCategory) {
			
			$subCategoryChoices = [];
			$subCategories = $this->subCategoryRepository->getAll($mainCategory->getId());
			
			foreach ($subCategories as $subCategory) {
				$subCategoryChoices[$subCategory->getId()] = $subCategory;
			}
			
			$categoryChoices[$mainCategory->getName()] = $subCategoryChoices ?? $mainCategory;
		}
		return $categoryChoices;
	}

    public function isSimilar(Category $category): void
    {
        if( $this->findBy(['name'=>$category->getName()])){
            throw new DuplicateKeyException('You have same category!');
        }
    }
	
	
}
