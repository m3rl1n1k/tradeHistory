<?php

namespace App\Service\Category;

class CategoryList
{

    private array $categories;

    /**
     * @return mixed
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param mixed $category
     */
    public function setCategory(mixed $category): void
    {
        $this->categories[] = $category;
    }

    public function setWithOutCategory($amount): void
    {
        $this->categories['without_category'] = $amount;
    }
}