<?php

namespace App\Trait;

use App\Entity\Transaction;
use App\Form\TransactionType;
use DateTime;
use Symfony\Component\Form\FormInterface;

trait TransactionTrait
{
    protected function getForm(Transaction $transaction): FormInterface
    {
        return $this->createForm(TransactionType::class, $transaction, [
            'category' => array_map(function ($category) {
                return $category['categories'];
            }, $this->parentCategoryRepository->getMainAndSubCategories()),
            'wallet' => $this->getUser()->getWallets(),
            'transaction' => $transaction,
            'date' => new DateTime()
        ]);
    }
}