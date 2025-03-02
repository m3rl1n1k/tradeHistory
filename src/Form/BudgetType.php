<?php

namespace App\Form;

use App\Entity\Budget;
use App\Form\CustomType\MonthType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['category'];
        $builder
            ->add('plannedAmount')
            ->add('month', MonthType::class, [
                'label' => 'Select Month',
                'required' => true
            ])
            ->add('category', ChoiceType::class, [
                'duplicate_preferred_choices' => false,
                'required' => false,
                'choices' => $category,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Category',
                'placeholder' => "Select category"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
            'category' => null,
        ]);
    }
}