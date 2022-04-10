<?php

namespace App\Modules\Storefront\Controller;

use App\Enum\SessionFlashType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app.home.')]
class HomeController extends AbstractController
{
    private string $sectionTitle = "Home";

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('', name: 'index')]
    public function index()
    {
        $products = $this->productRepository->findBy([], [
            'name'      => 'ASC'
        ]);

        $product = null;
        foreach ($products as $productive) {
            $product = $productive;
            break;
        }

//        dd($product->getImages()[0]->getImageName());
//
//        foreach ($product->getImages() as $image) {
//            dd($image);
//        }

        $pageTitle = "Store";

        return $this->render('storefront/home/index.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'products'          => $products
        ]);
    }

    #[Route('/contact', name: 'contact', methods: ['GET'])]
    public function contact()
    {

    }

    #[Route('/about', name: 'about', methods: ['GET'])]
    public function about()
    {

    }
}