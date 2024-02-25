<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Wallet;
use App\Wallet\Enum\CurrencyEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Length;

class WalletType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('currency', ChoiceType::class, [
				'choices' => CurrencyEnum::associativeArray()
			])
			->add('amount', NumberType::class, [
				'required' => false,
			])
			->add('name', TextType::class, [
				'constraints' => [
					new Length([
						'max' => 20
					])
				]
			]);
	}
	
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Wallet::class,
		]);
	}
}
