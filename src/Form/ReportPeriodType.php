<?php

namespace App\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateFrom', DateType::class,[
                'data' => new DateTime('-1 week'),
            ])
            ->add('dateEnd', DateType::class,[
                'data' => new DateTime(),
                'attr' => [
                    'max' => (new DateTime())->format('Y-m-d'),
                ],
            ]);
        $builder->setAttribute('id', 'report_period');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
