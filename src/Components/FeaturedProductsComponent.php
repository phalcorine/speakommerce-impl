<?php

namespace App\Components;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('featured_products')]
class FeaturedProductsComponent
{
    public ?int $productId;
    /** @var Product[] */
    public array $products;
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function mount(int $categoryId, int $productId)
    {
        /** @var ProductCategory $category */
        $category = $this->entityManager->getReference(ProductCategory::class, $categoryId);

        $products = $this->productRepository->findBy([
            'category'      => $category
        ]);

        $this->products = $products;

        $this->productId = $productId;
    }

}