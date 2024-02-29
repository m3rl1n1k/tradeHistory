<?php

namespace App\Form;

use App\Entity\Transfer;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferType extends AbstractType
{
	public function __construct(protected WalletRepository $walletRepository)
	{
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$user = $options['user'];
		$wallets = $this->walletRepository->getAll($user);
		$builder
			->add('amount')
			->add('fromWallet', EntityType::class, [
				'class' => Wallet::class,
				'choice_label' => 'number',
				'choices' => $wallets
			])
			->add('toWallet', EntityType::class, [
				'class' => Wallet::class,
				'choice_label' => 'number',
				'choices' => $wallets
			]);
	}
	
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Transfer::class,
			'user' => User::class,
		]);
	}
}
