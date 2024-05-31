<?php

namespace App\EventSubscriber;

use App\Service\SettingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{

    public function __construct(protected Environment $twig, protected SettingService $settingService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(): void
    {
        $this->twig->addGlobal('user_settings', $this->settingService::getSettings());
    }
}
