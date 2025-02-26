<?php

namespace App;

use App\Doctrine\Compiler\DoctrineTypeCompilerPass;
use App\Doctrine\Type\EncryptedStringType;
use App\Service\CryptService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        $container = $this->getContainer();
        $cryptService = $container->get(CryptService::class);

        // Встановлюємо CryptService у кастомний Doctrine Type
        EncryptedStringType::setCryptic($cryptService);
    }

    /**
     * @throws Exception
     */
    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new DoctrineTypeCompilerPass());
    }
}