<?php

namespace App\Form;

use App\Entity\UserSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coloredCategories', CheckboxType::class, [
                'label' => 'Colored Categories',
                'required' => false,
                'data' => $options['data']->isColoredCategories()
            ])
            ->add('coloredParentCategories', CheckboxType::class, [
                'label' => 'Colored Parent Categories',
                'required' => false,
                'data' => $options['data']->isColoredParentCategories()
            ])
            ->add('colorExpenseChart', ColorType::class, [
                'label' => 'Color Expense Chart',
                'data' => $options['data']->getColorExpenseChart()
            ])
            ->add('colorIncomeChart', ColorType::class, [
                'label' => 'Color Income Chart',
                'data' => $options['data']->getColorIncomeChart()
            ])
            ->add('transactionsPerPage', IntegerType::class, [
                'label' => 'Transactions Per Page',
                'data' => $options['data']->getTransactionsPerPage()
            ])
            ->add('defaultColorForCategoryAndParent', ColorType::class, [
                'label' => 'Default Color For Category And Parent',
                'data' => $options['data']->getDefaultColorForCategoryAndParent()
            ])
            ->add('showColorInTransactionList', CheckboxType::class, [
                'label' => 'Show color in transaction list',
                'required' => false,
                'data' => $options['data']->isShowColorInTransactionList()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSetting::class,
        ]);
    }
}
