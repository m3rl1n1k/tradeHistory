<?php

namespace App\DataFixtures;

use App\Entity\ParentCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParentCategoryFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $parentCategory = new ParentCategory();
            $parentCategory->setUser($this->getReference('user-' . rand(1, 2), User::class));
            $parentCategory->setName('category ' . $i);
            $parentCategory->setColor('#84e89f');
            $manager->persist($parentCategory);
            $this->addReference('parentCategory' . $i, $parentCategory);
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