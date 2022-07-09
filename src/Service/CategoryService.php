<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryService
{
    public function __construct
    (
        CategoryRepository $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoryByName(string $name): Category
    {
       return $this->categoryRepository->findOneBy(['name' => $name]);
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }
}