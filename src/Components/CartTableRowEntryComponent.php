<?php

namespace App\Components;

use App\Enum\SessionFlashType;
use App\Modules\Storefront\Model\CartItem;
use App\Modules\Storefront\Service\SessionCartService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('cart_table_row_entry')]
class CartTableRowEntryComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public CartItem $cartItem;
    private SessionCartService $cartService;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        SessionCartService $cartService
    )
    {
        $this->cartService = $cartService;
        $this->logger = $logger;
    }

    #[LiveAction]
    public function removeItemFromCart(): RedirectResponse
    {
        $this->cartService->removeItemFromCart($this->cartItem->getProductId());

        $this->addFlash(
            SessionFlashType::ERROR,
            "Item removed from cart..."
        );

        return $this->redirectToRoute('app.cart.index');
    }
}