<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\SubCategory;

class SubCategoryService
{
    public function mainColor(Category $parent, SubCategory $category, $noColor): SubCategory|string|null
    {
        // if parent set & category set -> set category.color
        // if parent not set & category set -> set category.color
        // if parent set color & category not set color -> set category.color = parent.color
        $parentColor = $parent->getColor();
        $categoryColor = $category->getColor();
        $result = 'Error in set color!';
        if ($noColor){
            return $category->setColor($parentColor);
        }
        if ($parentColor !== null && $categoryColor === null) {
            $result = $category->setColor($parentColor);
        }
        if ($parentColor === null && $categoryColor !== null) {
            $result = $category->setColor($categoryColor);
        }
        if ($parentColor !== null && $categoryColor !== null)
        {
            $result = $category->setColor($categoryColor);
        }
        return $result;
    }
}