<?php

namespace App\Controller;

use App\Trait\RepositoryTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MethodNameNotConfiguredException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use TargetPathTrait;

    protected function validateSimilarName(string $entityName, object $entity, EntityManagerInterface $em, array $options = []): bool
    {
        $this->methodIsAvailable('getName', $entity);
        /** @var RepositoryTrait $repo */
        $repo = $this->getRepository($entityName, $em);
        $name = $entity->getName();
        $metadata = $em->getClassMetadata(get_class($entity));
        if (isset($options['flag']) && $options['flag'] === 'edit') {
            return true;
        }
        if (!$metadata->hasField('user')) {
            $res = $repo->hasSame(['name' => $name]);
        } else {
            $res = $repo->hasSame(['name' => $name, 'user' => $this->getUser()->getId()]);
        }
        if (!$res) {
            $this->addFlash('error', "You already category with name: $name");
        }
        return $res;
    }

    private function methodIsAvailable(string $methodName, $entity): void
    {
        if (!method_exists($entity, $methodName)) {
            throw  new MethodNameNotConfiguredException();
        }
    }

    private function getRepository(string $entityName, EntityManagerInterface $em): EntityRepository
    {
        return $em->getRepository($entityName);
    }

    protected function validateIsMainWallet(string $entityName, object $entity, EntityManagerInterface $em, array $options = []): bool
    {
        $this->methodIsAvailable('isMain', $entity);
        /** @var RepositoryTrait $repo */
        $repo = $this->getRepository($entityName, $em);
        $mainWallet = $repo->hasSame(['isMain' => true, 'user' => $this->getUser()->getId()]);
        $isMain = $options['data']->isMain() === true;
        if ($isMain && !$mainWallet) {
            $this->addFlash('error', "You already have main wallet");
        }
        return $mainWallet ? $isMain : !$isMain;
    }
}