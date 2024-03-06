<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use App\Enum\CurrencyEnum;
use App\Enum\TransactionEnum;
use DateTime;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

;

class TransactionType extends AbstractType
{
	public function __construct(protected Security              $security,
								protected UrlGeneratorInterface $urlGenerator)
	{
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$category = $options['category'];
		$wallets = $options['wallet'];
		$user = $options['user'];
		$builder
			->add('wallet', ChoiceType::class, [
				'choice_label' => function (Wallet $wallet) {
					return $wallet->getName() ?? $wallet->getNumber();
				},
				'choice_value' => 'id',
				'choices' => $wallets
			])
			->add('amount', MoneyType::class, [
				'currency' => '' ?? $user->getCurrency()
			])
			->add('category', ChoiceType::class, [
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
			->add('date', DateType::class, [
				'data' => new DateTime(),
			])
			->add('description', TextareaType::class, [
				'required' => false,
				'attr' => [
					'max' => 255,
					'height' => 140
				]
			]);
	}
	
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Transaction::class,
			'category' => null,
			'wallet' => Wallet::class,
			'user' => User::class
		]);
	}
}
