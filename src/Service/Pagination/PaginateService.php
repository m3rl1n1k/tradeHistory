<?php

namespace App\Service\Pagination;

use App\Service\SettingService;
use Doctrine\ORM\Query;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class PaginateService implements PaginateInterface
{
    public function __construct(public SettingService $settingService)
    {
    }

    public function paginate(Query $query, Request $request, bool $inf = false): Pagerfanta
    {
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        $pagerfanta->setMaxPerPage(!$inf ? SettingService::getTransactionsPerPage() : $pagerfanta->count());

        return $pagerfanta;
    }
}