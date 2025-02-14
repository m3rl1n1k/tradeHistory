<?php

namespace App\Form;

use App\Entity\Transfer;
use App\Entity\Wallet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $label = function ($wallet) {
            $main = $wallet->getName() ?? $wallet->getNumber();
            return $main . " | " . $wallet->getAmount() . " " . $wallet->getCurrency();
        };

        $builder
            ->add('walletOut', EntityType::class, [
                'class' => Wallet::class,
                'choice_label' => $label,
            ])
            ->add('walletIn', EntityType::class, [
                'class' => Wallet::class,
                'choice_label' => $label,
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
        ]);
    }
}
