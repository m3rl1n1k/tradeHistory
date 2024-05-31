<?php

namespace App\Service;

use App\Entity\Category;

class CategoryService
{
    public function mainColor(Category $category, $form): Category|string|null
    {

        $parent = $this->getParent($form);
        $noColor = $this->getNoColor($form);
        // if parent set & category set -> set category.color
        // if parent not set & category set -> set category.color
        // if parent set color & category not set color -> set category.color = parent.color
        $parentColor = $parent->getColor();
        $categoryColor = $category->getColor();
        $result = 'Error in set color!';
        if ($noColor) {
            return $category->setColor($parentColor);
        }
        if ($parentColor !== null && $categoryColor === null) {
            $result = $category->setColor($parentColor);
        }
        if ($parentColor === null && $categoryColor !== null) {
            $result = $category->setColor($categoryColor);
        }
        if ($parentColor !== null && $categoryColor !== null) {
            $result = $category->setColor($categoryColor);
        }
        return $result;
    }

    protected function getParent($form)
    {
        return $form->getData()->getParentCategory();
    }

    protected function getNoColor($form)
    {
        if ($form->get('no_color') === null) {
            return "no set color";
        }
        return $form->get('no_color')->getData();
    }
}