<?php

namespace App\Trait;

trait RepositoryTrait
{
    public function hasSame(array $criteria): ?bool
    {
        return $this->findOneBy($criteria) === null;
    }
}