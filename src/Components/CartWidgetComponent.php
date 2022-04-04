<?php

namespace App\Components;

use App\Modules\Storefront\Model\CartItem;
use App\Modules\Storefront\Service\SessionCartService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('cart_widget')]
class CartWidgetComponent
{
    private SessionCartService $cartService;

    /** @var CartItem[] */
    public array $cartItems = [];
    public int $totalPrice = 0;

    public function __construct(SessionCartService $cartService)
    {
        $this->cartService = $cartService;
        $this->loadCartItems();
    }

    public function loadCartItems()
    {
        $cart = $this->cartService->getCart();
        $this->cartItems = $cart->getItems();
        $this->totalPrice = $cart->getTotalPrice();
    }
}