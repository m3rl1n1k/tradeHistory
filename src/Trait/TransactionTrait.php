<?php

namespace App\Trait;

use App\Entity\Transaction;
use App\Form\TransactionType;
use Symfony\Component\Form\FormInterface;

trait TransactionTrait
{
    protected function getForm(Transaction $transaction): FormInterface
    {
        return $this->createForm(TransactionType::class, $transaction, [
            'category' => $this->parentCategoryRepository->getMainAndSubCategories(),
            'wallet' => $this->walletRepository->getAll(),
        ]);
    }
}