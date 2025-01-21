<?php

namespace App\Trait;

use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;

trait RepositoryTrait
{
    public function hasSimilarName(array $criteria, $message): void
    {
        if ($this->findBy($criteria)) {
            throw new DuplicateKeyException($message);
        }
    }
}