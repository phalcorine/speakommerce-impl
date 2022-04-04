<?php

namespace App\Components;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('navbar_product_category_list')]
class NavbarProductCategoryListComponent
{
    private ProductCategoryRepository $categoryRepository;

    public function __construct(ProductCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return ProductCategory[]
     */
    public function getCategories(): array
    {
        $categories = $this->categoryRepository->findBy([], [
            'name'      => 'ASC'
        ]);

        return $categories;
    }
}