<?php

namespace App\Service;

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

    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }
}