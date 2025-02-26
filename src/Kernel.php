<?php

namespace App;

use App\Doctrine\Compiler\DoctrineTypeCompilerPass;
use App\Doctrine\Type\EncryptedStringType;
use App\Service\Interfaces\CrypticInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @throws \Exception
     */
    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new DoctrineTypeCompilerPass());
    }

    public function boot(): void
    {
        parent::boot();

        $container = $this->getContainer();
        $cryptService = $container->get(CrypticInterface::class);

        // Встановлюємо CryptService у кастомний Doctrine Type
        EncryptedStringType::setCryptic($cryptService);
    }
}