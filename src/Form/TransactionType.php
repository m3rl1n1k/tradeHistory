<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Enum\TransactionTypeEnum;
use DateTime;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    /**
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['category'];
        $wallets = $options['wallet'];
        $transaction = $options['transaction'];
        $date = $options['date'];
        $transactionDate = $transaction && $transaction->getDate() ? $transaction->getDate() : $date;
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
                'error_bubbling' => true,
                'invalid_message' => 'Not walid data for amount!',
                'attr' => [
                    'type' => 'number',
                    'step' => 0.01
                ]
            ])
            ->add('category', ChoiceType::class, [
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
                    'choices' => TransactionTypeEnum::transactionTypes(),
                ])
            ->add('date', DateType::class, [
                'attr' => [
                    'max' => (new DateTime())->format('Y-m-d'),
                ],
                'widget' => 'single_text',
                'data' => $transactionDate,
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
            'category' => null,
            'transaction' => null,
            'date' => new DateTime()
        ]);
    }
}
