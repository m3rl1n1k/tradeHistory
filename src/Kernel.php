<?php

namespace App;

use App\Doctrine\Compiler\DoctrineTypeCompilerPass;
use App\Doctrine\Type\EncryptedStringType;
use App\Service\Interfaces\CrypticInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        $container = $this->getContainer();
        /** @var CrypticInterface $cryptService */
        $cryptService = $container->get(CrypticInterface::class);
        EncryptedStringType::setCryptic($cryptService);

        // Set locale dynamically based on session or request
        if ($this->container->has('request_stack')) {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            if ($request instanceof Request) {
                $this->container->get('translator')->setLocale($request->getLocale());
            }
        }
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