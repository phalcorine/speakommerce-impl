<?php

namespace App\Modules\Storefront\Controller;

use App\Enum\SessionFlashType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'app.products.')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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

    #[Route('/{slug}', name: 'details', methods: ['GET'])]
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

        $pageTitle = $product->getName();

        return $this->render('storefront/products/details.html.twig', [
            'pageTitle'     => $pageTitle,
            'product'       => $product
        ]);
    }
}