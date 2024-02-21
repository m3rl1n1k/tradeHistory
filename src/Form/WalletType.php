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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class WalletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', ChoiceType::class, [
				'choices' => [
					'PLN' => CurrencyEnum::PLN,
					'EUR' => CurrencyEnum::EUR,
					'UAH' => CurrencyEnum::UAH,
					'USD' => CurrencyEnum::USD,
				]
			])
            ->add('amount', NumberType::class)
            ->add('isDefault', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wallet::class,
        ]);
    }
}
