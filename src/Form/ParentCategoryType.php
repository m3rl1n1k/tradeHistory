<?php

namespace App\Form;

use App\Entity\ParentCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'trim' => true,
            ])
            ->add('color', ColorType::class, [
                'required' => false,
                'attr' => [
                    "value" => "#1c6263"
                ]
            ])
            ->add('no_color', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'With out color'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParentCategory::class,
        ]);
    }
}
