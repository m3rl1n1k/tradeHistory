<?php

namespace App\Form;

use App\Entity\Wallet;
use App\Enum\CurrencyEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
	use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class WalletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', ChoiceType::class, [
		    'choices' => CurrencyEnum::associativeArray(),
		    'label'=> 'Currency'
            ])
            ->add('amount', NumberType::class, [
		    'required' => false,
		    'label' => 'Amount'
            ])
            ->add('name', TextType::class, [
		    'required' => false,
		    'label'=>'Name',
                'constraints' => [
                    new Length([
                        'max' => 20
                    ])
                ]
            ])
            ->add('isMain', CheckboxType::class, [
                'required' => false,
                'label' => 'Is main wallet?',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wallet::class,
        ]);
    }
}
