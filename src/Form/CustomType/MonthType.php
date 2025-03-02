<?php

namespace App\Form\CustomType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MonthType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['type'] = 'month';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            $resolver->setDefaults([
                'html5' => false,
                'widget' => 'single_text', // Важливо: змушує Symfony згенерувати простий <input>
                'format' => 'yyyy-MM', // Вказуємо правильний формат
                'input' => 'string', // Зберігаємо значення як string
                'required' => true,
            ])
        ]);
    }

    public function getParent(): string
    {
        return DateType::class;
    }
}