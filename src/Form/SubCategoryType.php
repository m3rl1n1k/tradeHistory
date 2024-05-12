<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Enum\ColorEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'trim' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choices' => $options['main_category'],
                'choice_label' => 'name',
                'label' => 'Parent category:'
            ])
            ->add('color', ColorType::class, [
                'required' => false,
            ])
            ->add('no_color', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "With out color"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
            'main_category' => Category::class,
            'values' => null
        ]);
    }
}
