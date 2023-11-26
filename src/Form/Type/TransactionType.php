<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'Incoming' => 1,
                'Expense' => 2,
                'Transaction' => 3
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}