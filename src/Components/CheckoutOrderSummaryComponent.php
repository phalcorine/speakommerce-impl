<?php

namespace App\Components;

use App\Enum\SessionFlashType;
use App\Modules\Storefront\Model\CartItem;
use App\Modules\Storefront\Service\SessionCartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('checkout_order_summary')]
class CheckoutOrderSummaryComponent extends AbstractController
{
    use DefaultActionTrait;

    /** @var CartItem[] */
    public array $cartItems;

    public float $totalPrice;
    private SessionCartService $cartService;

    public function __construct(SessionCartService $cartService)
    {
        $this->cartService = $cartService;
        $this->loadItems();
    }

    #[LiveAction]
    public function removeProductFromCart(#[LiveArg] int $productId)
    {
        $cart = $this->cartService->getCart();
        $cart->removeFromCart($productId);
        $this->cartService->setCart($cart);
    }

    private function loadItems()
    {
        $cart = $this->cartService->getCart();
        $this->cartItems = $cart->getItems();
        $this->totalPrice = $cart->getTotalPrice();
        if(count($this->cartItems) <= 0) {
            $this->addFlash(
                SessionFlashType::NOTICE,
                "You can not proceed to checkout as you have no items in your cart..."
            );

            return $this->redirectToRoute('app.home.index');
        }
    }
}