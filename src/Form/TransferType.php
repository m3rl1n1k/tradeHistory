<?php

namespace App\Form;

use App\Entity\Transfer;
use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $wallets = $options['wallets'];
        $label = function ($wallet) {
            $main = $wallet->getName() ?? $wallet->getNumber();
            return $main . " | " . $wallet->getAmount() . " " . $wallet->getCurrency();

        };
//        dd($wallet);
        $builder
            ->add('walletOut', ChoiceType::class, [
                'placeholder' => "Select wallet",
                'choice_label' => $label,
                'choice_value' => 'id',
                'choices' => $wallets
            ])
            ->add('walletIn', ChoiceType::class, [
                'placeholder' => "Select wallet",
                'choice_label' => $label,
                'choice_value' => 'id',
                'choices' => $wallets
            ])
            ->add('amount', NumberType::class, [
                'attr' => [
                    'step' => 0.01,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transfer::class,
            'amount' => null,
            'wallets' =>[]
        ]);
    }
}