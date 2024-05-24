<?php

namespace App\Form;

use App\Entity\Transfer;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
    public function __construct(protected WalletRepository $walletRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $wallets = $this->walletRepository->getAll();
        $label = function (Wallet $wallet) {
            return (!empty($wallet->getName()) ? $wallet->getName() : $wallet->getNumber()) . " | " . $wallet->getAmount() . " " . $wallet->getCurrency();
        };
        $builder
            ->add('amount', NumberType::class)
            ->add('fromWallet', EntityType::class, [
                'class' => Wallet::class,
                'choice_label' => $label,
                'choices' => $wallets
            ])
            ->add('toWallet', EntityType::class, [
                'class' => Wallet::class,
                'choice_label' => $label,
                'choices' => $wallets
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transfer::class,
        ]);
    }
}
