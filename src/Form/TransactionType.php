<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Transaction\TransactionEnum;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    private string $date;

    public function __construct()
    {
        $this->date = (new DateTime())->format('Y-m-d\TH:i');
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['category'];
        $wallets = $options['wallet'];
        $builder
            ->add('wallet', ChoiceType::class, [
                'placeholder' => "Select wallet",
                'choice_label' => function (Wallet $wallet) {
                    return (!empty($wallet->getName()) ? $wallet->getName() : $wallet->getNumber()) . " | " . $wallet->getAmount() . " " . $wallet->getCurrency();
                },
                'choice_value' => 'id',
                'choices' => $wallets
            ])
            ->add('amount', NumberType::class, [
                'attr' => [
                    'step' => 0.01
                ]
            ])
            ->add('Category', ChoiceType::class, [
                'duplicate_preferred_choices' => false,
                'required' => false,
                'choices' => $category,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Category',
                'placeholder' => "Select category"
            ])
            ->add('type', ChoiceType::class,
                [
                    'choices' => TransactionEnum::transactionTypes(),
                ])
            ->add('date', DateTimeType::class, [
                'data' => new DateTime(),
                'attr' => [
                    'max' => $this->date,
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'max' => 255
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'wallet' => Wallet::class,
            'category' => null
        ]);
    }
}
