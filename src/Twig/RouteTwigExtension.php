<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouteTwigExtension extends AbstractExtension
{
	
	public function __construct(protected RequestStack $requestStack)
	{
	}
	
	public function getFunctions(): array
	{
		return [
			new TwigFunction('route', [$this, 'routeCheck']),
			new TwigFunction('isActive', [$this, 'activePage']),
		];
	}
	
	public function routeCheck(string $route): bool
	{
		return $this->requestStack->getCurrentRequest()->getPathInfo() === $route ?? false;
	}
	
	public function activePage(string $route, $activeClass = "active")
	{
		return$this->requestStack->getCurrentRequest()->getPathInfo() == $route ? $activeClass : "-";
	}
}