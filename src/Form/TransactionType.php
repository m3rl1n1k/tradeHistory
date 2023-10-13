<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Transaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Income' => 'INCOME',
                    'Expense' => 'EXPENSE',
                ],
                'label' => 'Select transaction type:',
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('date', DateType::class, [
                'data' => new \DateTime(),
            ])
            ->add('category_id', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'empty_data' => null,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
