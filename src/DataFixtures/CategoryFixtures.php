<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const CATEGORY_REFERENCE = 'category-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName('Category ' . $i);
            $category->setParentCategory($this->getReference(ParentCategoryFixtures::PARENT_CATEGORY_REFERENCE . rand(0, 19)));
            $this->addReference(self::CATEGORY_REFERENCE . $i, $category);
            $manager->persist($category);
            $manager->flush();
        }


    }

    public function getDependencies(): array
    {
        return [
            ParentCategoryFixtures::class
        ];
    }
}
