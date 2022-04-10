<?php

namespace App\Modules\Storefront\Service;

use App\Entity\Product;
use App\Modules\Storefront\Model\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionCartService
{
    const SESSION_CART_KEY = 'session-cart';

    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    )
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function addItemToCart(int $productId, int $quantity = 1)
    {
        $product = $this->entityManager->getReference(Product::class, $productId);
        $cart = $this->getCart();
        $cart->addToCart($product, $quantity);;
        $this->setCart($cart);
    }

    public function removeItemFromCart(int $productId)
    {
        $cart = $this->getCart();
        $cart->removeFromCart($productId);
        $this->setCart($cart);
    }

    public function getCart(): Cart
    {
        /** @var Cart $cart */
        return $this->requestStack->getSession()->get(self::SESSION_CART_KEY, new Cart());
    }

    public function setCart(Cart $cart)
    {
        $this->requestStack->getSession()->set(self::SESSION_CART_KEY, $cart);
    }
}