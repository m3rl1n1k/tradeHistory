<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function __construct(protected Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'currency' => 'PLN'
            ])
            ->add('type', Type\TransactionType::class)
            ->add('date', DateTimeType::class, [
                'html5' => true
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'max' => 255,
                ]
            ])
            ->add('user_id', HiddenType::class, [
                'attr' => [
                    'value' => $this->security->getUser()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
