<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\ParentCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName('Category ' . $i);
            $category->setParentCategory($this->getReference('parentCategory' . rand(1, 9), ParentCategory::class));
            $manager->persist($category);
            $this->addReference('category-' . $i, $category);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            WalletFixture::class,
            ParentCategoryFixture::class
        ];
    }
}