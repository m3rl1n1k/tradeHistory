<?php

namespace App\DataFixtures;

use App\Entity\ParentCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ParentCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const PARENT_CATEGORY_REFERENCE = 'parent-category-';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $parentCategory = new ParentCategory();
            $parentCategory->setName('Parent Category ' . $i);
            $parentCategory->setUser($this->getReference(UserFixtures::USER_REFERENCE));
            $manager->persist($parentCategory);

            // Store a reference to use it in other fixtures
            $this->addReference(self::PARENT_CATEGORY_REFERENCE . $i, $parentCategory);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
