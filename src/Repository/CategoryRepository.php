<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
	public function __construct(ManagerRegistry $registry, protected SubCategoryRepository $subCategoryRepository)
	{
		parent::__construct($registry, Category::class);
	}

	public function getAll($user): array
	{
		return $this->findBy(['user' => $user]);
	}
	
	public function getOneBy(int $id): Category
	{
		return $this->findOneBy(['id' => $id]);
	}
	
	public function categoryUpdate(Category $category, Category $update, ?User $user, int $id): void
	{
		$category->setUser($user);
		$category->setName($update->getName());
		$category->setId($id);
		
	}
	
	public function getCategories(int $user): array
	{
		return $this->findBy(['user' => $user]);
	}
	
	public function getMainAndSubCategories(User $user): array
	{
		/** @var Category $mainCategory */
		$mainCategories = $this->getAll($user);
		$categoryChoices = [];
		
		foreach ($mainCategories as $mainCategory) {
			$subCategoryChoices = [];
			$subCategories = $this->subCategoryRepository->findBy(['category' => $mainCategory->getId()]);
			$subCategoryChoices['main'] = $mainCategory;
			foreach ($subCategories as $subCategory) {
				
				$subCategoryChoices['main'] = $mainCategory;
				$subCategoryChoices[$subCategory->getId()] = $subCategory;
			}
			$categoryChoices[$mainCategory->getName()] = $subCategoryChoices ?? $mainCategory;
		}
		return $categoryChoices;
	}
	
	
}
