<?php

namespace App\Trait;

use App\Entity\Budget;
use App\Form\BudgetType;
use Symfony\Component\Form\FormInterface;

trait BudgetTrait
{
    protected function getForm(Budget $budget): FormInterface
    {
        return $this->createForm(BudgetType::class, $budget, [
            'category' => array_map(function ($category) {
                return $category['categories'];
            }, $this->parentCategoryRepository->getMainAndSubCategories()),
        ]);
    }
}