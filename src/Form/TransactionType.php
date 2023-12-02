<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Enum\TransactionEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function __construct(protected Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'currency' => 'PLN'
            ])->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'choice_label' => function (Category $category): string {
                    return $category->getName();
                }
            ])
            ->add('type', ChoiceType::class,
                [
                    'choices' => [
                        "Income" => TransactionEnum::INCOME,
                        "Expense" => TransactionEnum::EXPENSE,
                        "Transaction" => TransactionEnum::TRANSACTION,
                    ]
                ])
            ->add('date', DateTimeType::class,[
                'data' => new DateTime(),
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'max' => 255,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
