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
                'data' => $options['data']['colored_categories']

            ])
            ->add('coloredParentCategories', CheckboxType::class, [
                'label' => 'Colored Parent Categories',
                'required' => false,
                'data' => $options['data']['colored_parent_categories']
            ])
            ->add('colorExpenseChart', ColorType::class, [
                'label' => 'Color Expense Chart',
                'data' => $options['data']['color_expense_chart']
            ])
            ->add('colorIncomeChart', ColorType::class, [
                'label' => 'Color Income Chart',
                'data' => $options['data']['color_income_chart']
            ])
            ->add('transactionsPerPage', IntegerType::class, [
                'label' => 'Transactions Per Page',
                'data' => $options['data']['transactions_per_page']
            ])
            ->add('defaultColorForCategoryAndParent', ColorType::class, [
                'label' => 'Default Color For Category And Parent',
                'data' => $options['data']['default_color_for_category_and_parent']
            ])
            ->add('categoriesWithoutColor', CheckboxType::class, [
                'label' => 'Categories Without Color',
                'required' => false,
                'data' => $options['data']['categories_without_color']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
