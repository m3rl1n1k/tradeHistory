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

	public function getMainAndSubCategories(User $user): array
	{
		/** @var Category $mainCategory */
		$mainCategories = $this->getAll($user);
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
	
//	public function getMainAndSubCategories(User $user): array
//	{
//		$mainCategories = $this->getAll($user);
//		$subCategories = $this->subCategoryRepository->findAll();
//		return array_merge($mainCategories, $subCategories);
//	}
	
	
}
