<?php

namespace App\Modules\Storefront\Controller;

use App\Enum\SessionFlashType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app.categories.')]
class CategoryController extends AbstractController
{
    private ProductCategoryRepository $categoryRepository;
    private ProductRepository $productRepository;

    public function __construct(
        ProductCategoryRepository $categoryRepository,
        ProductRepository $productRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index()
    {

    }

    #[Route('/{slug}', name: 'details', methods: ['GET'])]
    public function categoryBySlug($slug): RedirectResponse|Response
    {
        $category = $this->categoryRepository->findOneBy([
            'slug'  => $slug
        ]);
        if(is_null($category)) {
             $this->addFlash(
                 SessionFlashType::ERROR,
                 "The category specified does not exists..."
             );

             return $this->redirectToRoute('app.categories.index');
        }

        $products = $this->productRepository->findBy([
            'category'      => $category
        ]);

        $categories = $this->categoryRepository->findBy([], [
            'name'      => 'ASC'
        ]);

        $pageTitle = $category->getName();

        return $this->render('storefront/categories/category_by_slug.html.twig', [
            'pageTitle'     => $pageTitle,
            'category'      => $category,
            'products'      => $products,
            'categories'    => $categories,
        ]);
    }
}