<?php

namespace App\Modules\Storefront\Controller;

use App\Modules\Storefront\Service\SessionCartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'app.cart.')]
class CartController extends AbstractController
{
    private SessionCartService $cartService;

    public function __construct(SessionCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index()
    {

    }

    #[Route('/checkout', name: 'checkout', methods: ['GET', 'POST'])]
    public function checkout()
    {

    }
}