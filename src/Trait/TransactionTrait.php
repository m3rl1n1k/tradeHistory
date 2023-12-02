<?php

namespace App\Trait;

use App\Entity\Transaction;
use Doctrine\ORM\Query;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

trait TransactionTrait
{
    public function accessDenied(Transaction $transaction): void
    {
        if ($this->getUser() !== $transaction->getUserId()) {
            throw $this->createAccessDeniedException('You don\'t have access! ');
        }
    }

    public function paginate(Query $query, Request $request, int $maxRecords = 10, bool $inf = false): Pagerfanta
    {
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        if ($inf) {
            $pagerfanta->setMaxPerPage($pagerfanta->count());
        } else {
            $pagerfanta->setMaxPerPage($maxRecords);
        }

        return $pagerfanta;
    }
}