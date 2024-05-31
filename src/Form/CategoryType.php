<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\ParentCategory;
use App\Service\SettingService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function __construct(protected SettingService $settingService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'trim' => true,
            ])
            ->add('ParentCategory', EntityType::class, [
                'class' => ParentCategory::class,
                'choices' => $options['main_ParentCategory'],
                'choice_label' => 'name',
                'label' => 'Parent category:'
            ]);
        if ($this->settingService::isCategoryWithColor()) {
            $builder->add('color', ColorType::class, [
                'required' => false,
                'attr' => [
                    "value" => $this->settingService::getDefaultColorForCategory()
                ]
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'main_ParentCategory' => Category::class,
            'values' => null
        ]);
    }
}
