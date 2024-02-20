<?php

namespace App\Transaction\Trait;

use App\Entity\User;
use App\Transaction\Entity\Transaction;
use Doctrine\ORM\Query;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

trait TransactionTrait
{
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