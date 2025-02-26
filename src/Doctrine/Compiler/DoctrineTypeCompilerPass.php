<?php

namespace App\Doctrine\Compiler;

use App\Doctrine\Type\EncryptedStringType;
use App\Service\Interfaces\CrypticInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineTypeCompilerPass implements CompilerPassInterface
{

    /**
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        if (!Type::hasType('encrypted_string')){
            Type::addType('encrypted_string', EncryptedStringType::class);
        }
        $definition = $container->getDefinition(EncryptedStringType::class);
        $definition->addMethodCall('setCryptic', [new Reference(CrypticInterface::class)]);
    }
}