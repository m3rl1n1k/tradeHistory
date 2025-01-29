<?php

namespace App\Form;

use App\Entity\ParentCategory;
use App\Service\SettingService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentCategoryType extends AbstractType
{
    public function __construct(protected SettingService $settingService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'trim' => true,
            ]);
        if ($this->settingService::isColoredParentCategories()) {
            $builder->add('color', ColorType::class, [
                'required' => false,
                'attr' => [
                    "value" => $this->settingService::getDefaultColorForCategoryAndParent()
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParentCategory::class,
        ]);
    }
}
