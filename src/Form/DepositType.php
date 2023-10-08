<?php

namespace App\Form;

use App\Entity\Deposit;
use App\Enum\DepositPercentEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('amount')
			->add('percent', ChoiceType::class, [
				'choices' => [
					'1%' => DepositPercentEnum::p1,
					'2%' => DepositPercentEnum::p2,
					'3%' => DepositPercentEnum::p3,
					'5%' => DepositPercentEnum::p5,
					'10%' => DepositPercentEnum::p10,
				]
			])
			->add('date_open', DateType::class, [
				'data' => new \DateTime()
			])
			->add('date_close', DateType::class, [
				'data' => new \DateTime()
			])
			->add('readyDate', ChoiceType::class, [
				'choices' => [
					'1 month' =>
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Deposit::class,
		]);
	}
}
