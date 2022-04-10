<?php

namespace App\Components;

use App\Entity\Product;
use App\Modules\Storefront\Service\SessionCartService;
use Psr\Log\LoggerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('add_to_cart_widget')]
class AddToCartWidgetComponent
{
    use DefaultActionTrait;

    private LoggerInterface $logger;
    private SessionCartService $cartService;

    public function __construct(
        SessionCartService $cartService,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->cartService = $cartService;
    }

    #[LiveProp]
    public Product $product;

    #[LiveProp(writable: true)]
    public int $quantity = 0;

    public bool $isAdded = false;

    #[LiveAction]
    public function addToCart()
    {
        $this->logger->info("====");
        $this->logger->info("Add to Cart");
        $this->logger->info("Product Name: " . $this->product->getName());
        $this->logger->info("Current Quantity: " . $this->quantity);
//        $this->productId = $productId;

        $this->cartService->addItemToCart($this->product->getId(), $this->quantity);
        $this->quantity = 1;
        $this->isAdded = true;
    }
}