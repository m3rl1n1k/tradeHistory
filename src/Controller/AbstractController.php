<?php

namespace App\Controller;

use App\Trait\RepositoryTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MethodNameNotConfiguredException;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function validateSimilarName(string $entityName, object $entity, EntityManagerInterface $em): void
    {
        if (!method_exists($entity, 'getName')) {
            throw  new MethodNameNotConfiguredException();
        }

        /** @var RepositoryTrait $repo */
        $repo = $em->getRepository($entityName);
        $name = $entity->getName();
        $repo->hasSimilarName(['name' => $name], "You have same category with name: $name");
    }
}