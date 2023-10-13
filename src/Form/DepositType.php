<?php

namespace App\Form;

use App\Entity\Deposit;
use App\Enum\DepositSettingsEnum;
use App\Services\DateTimeService;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositType extends AbstractType
{
	public function __construct(
		protected DateTimeService $dateTime
	)
	{
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
        $date = new DateTime();
		$builder
			->add('amount')
			->add('percent', ChoiceType::class, [
				'choices' => [
					'1%' => DepositSettingsEnum::p1,
					'2%' => DepositSettingsEnum::p2,
					'3%' => DepositSettingsEnum::p3,
				]
			])
			->add('date_open', DateTimeType::class, [
				'data' => $date,
                'html5' => false,
                'format' => "d m Y"
			])
			->add('date_close', ChoiceType::class, [
				'choices' => [
					DepositSettingsEnum::MONTH1 => $this->dateTime->calculateDifferent(DepositSettingsEnum::MONTH1),
					DepositSettingsEnum::MONTH2 => $this->dateTime->calculateDifferent(DepositSettingsEnum::MONTH2),
					DepositSettingsEnum::MONTH3 => $this->dateTime->calculateDifferent(DepositSettingsEnum::MONTH3),
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
