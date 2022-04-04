<?php

namespace App\Modules\Storefront\Model;

use App\Entity\Product;

class CartItem
{
    private ?int $productId = null;

    private ?Product $product = null;

    private int $quantity = 0;

    public function getSubTotal()
    {
        return $this->product->getPrice() * $this->quantity;
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * @param int|null $productId
     * @return CartItem
     */
    public function setProductId(?int $productId): CartItem
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return CartItem
     */
    public function setProduct(?Product $product): CartItem
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return CartItem
     */
    public function setQuantity(int $quantity): CartItem
    {
        $this->quantity = $quantity;
        return $this;
    }
}