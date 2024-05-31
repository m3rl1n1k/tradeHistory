<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coloredCategories', CheckboxType::class, [
                'label' => 'Colored Categories',
                'required' => false,
                'data' => $options['data']['coloredCategories']

            ])
            ->add('coloredParentCategories', CheckboxType::class, [
                'label' => 'Colored Parent Categories',
                'required' => false,
                'data' => $options['data']['coloredParentCategories']
            ])
            ->add('colorExpenseChart', ColorType::class, [
                'label' => 'Color Expense Chart',
                'data' => $options['data']['colorExpenseChart']
            ])
            ->add('colorIncomeChart', ColorType::class, [
                'label' => 'Color Income Chart',
                'data' => $options['data']['colorIncomeChart']
            ])
            ->add('transactionsPerPage', IntegerType::class, [
                'label' => 'Transactions Per Page',
                'data' => $options['data']['transactionsPerPage']
            ])
            ->add('defaultColorForCategoryAndParent', ColorType::class, [
                'label' => 'Default Color For Category And Parent',
                'data' => $options['data']['defaultColorForCategoryAndParent']
            ])
            ->add('categoriesWithoutColor', CheckboxType::class, [
                'label' => 'Categories Without Color',
                'required' => false,
                'data' => $options['data']['categoriesWithoutColor']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
