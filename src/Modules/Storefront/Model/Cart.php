<?php

namespace App\Modules\Storefront\Model;

use App\Entity\Product;
use JetBrains\PhpStorm\Pure;

class Cart
{
    /** @var CartItem[] */
    private array $items = [];

    public function addToCart(Product $product, int $quantity)
    {
        $existingItem  = null;
        foreach ($this->items as $item) {
            if ($item->getProductId() == $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if($existingItem != null) {
            $existingItem->setQuantity($existingItem->getQuantity() + $quantity);
            return;
        }

        $cart = (new CartItem())
            ->setProduct($product)
            ->setProductId($product->getId())
            ->setQuantity($quantity);

        $this->items[] = $cart;
    }

    public function removeFromCart(int $productId)
    {
        /** @var CartItem[] $newItems */
        $newItems = [];
        foreach ($this->items as $item) {
            if ($item->getProductId() != $productId) {
                $newItems[] = $item;
            }
        }

        $this->items = $newItems;
    }

    /**
     * @return CartItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    #[Pure]
    public function getTotalPrice(): float|int
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $totalPrice;
    }
}