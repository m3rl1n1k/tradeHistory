<?php

namespace App\Form;

use App\Category\Entity\Category;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionEnum;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TransactionType extends AbstractType
{
	public function __construct(protected Security              $security,
								protected UrlGeneratorInterface $urlGenerator)
	{
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$category = $options['category'];
		$builder
			->add('amount', MoneyType::class, [
				'currency' => 'CP'
			])->add('category', ChoiceType::class, [
				'choices' => $category,
				'required' => false,
				'choice_label' => function (Category $category): string {
					return $category->getName();
				}
			])
			->add('type', ChoiceType::class,
				[
					'choices' => [
						"Expense" => TransactionEnum::EXPENSE,
						"Income" => TransactionEnum::INCOME,
						"Transaction" => TransactionEnum::TRANSACTION,
					]
				])
			->add('date', DateType::class, [
				'data' => new DateTime(),
			])
			->add('description', TextareaType::class, [
				'required' => false,
				'attr' => [
					'max' => 255,
				]
			]);
	}
	
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Transaction::class,
			'category' => null
		]);
	}
}
