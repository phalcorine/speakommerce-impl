<?php

namespace App\Modules\Storefront\Controller;

use App\Enum\SessionFlashType;
use App\Repository\MediaFileRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'app.products.')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private ProductCategoryRepository $categoryRepository;
    private MediaFileRepository $mediaFileRepository;

    public function __construct(
        ProductCategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        MediaFileRepository $mediaFileRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->mediaFileRepository = $mediaFileRepository;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->productRepository->findBy([], [
            'name'      => 'ASC'
        ]);

        $pageTitle = "All Products";

        return $this->render('storefront/products/index.html.twig', [
            'pageTitle'     => $pageTitle,
            'products'      => $products
        ]);
    }

    #[Route('/product/{slug}', name: 'details', methods: ['GET'])]
    public function details($slug): RedirectResponse|Response
    {
        $product = $this->productRepository->findOneBy([
            'slug'      => $slug
        ]);
        if(is_null($product)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product specified does not exist..."
            );

            return $this->redirectToRoute('app.products.index');
        }

        $mediaFiles = $this->mediaFileRepository->findBy([
            'product'       => $product
        ]);

        $pageTitle = $product->getName();

        return $this->render('storefront/products/details.html.twig', [
            'pageTitle'     => $pageTitle,
            'product'       => $product,
            'mediaFiles'    => $mediaFiles
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(string $query)
    {
        $products = $this->productRepository->searchByNameOrDescription($query);
        $categories = $this->categoryRepository->findBy([], [
            'name'      => 'ASC'
        ]);
        $pageTitle = "Showing Search Results - " . $query;

        return $this->render('storefront/products/search.html.twig', [
            'pageTitle'     => $pageTitle,
            'products'      => $products,
            'categories'    => $categories
        ]);
    }
}