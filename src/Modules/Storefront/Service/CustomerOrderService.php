<?php

namespace App\Modules\Storefront\Service;

use App\Entity\Order;
use App\Entity\OrderBillingDetail;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\OrderStatusType;
use App\Modules\Storefront\Model\Cart;
use App\Modules\Storefront\Model\CheckoutForm;
use App\Utility\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerOrderService
{
    private SessionCartService $cartService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        SessionCartService $cartService
    )
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function placeOrder(int $customerId, CheckoutForm $checkoutForm)
    {
        /** @var User $customer */
        $customer = $this->entityManager->getReference(User::class, $customerId);

        $cart = $this->cartService->getCart();
        if(count($cart->getItems()) <= 0) {
            throw new \LogicException("Can not place order as there is not product item in the cart!");
        }

        try {
            $order = (new Order())
                ->setToken(TokenGenerator::generateOrderToken())
                ->setUser($customer)
                ->setCustomerName($customer->getName())
                ->setTotalPrice($cart->getTotalPrice())
                ->setStatus(OrderStatusType::PENDING);
            $this->entityManager->persist($order);

            foreach ($cart->getItems() as $cartItem) {
                /** @var Product $product */
                $product = $this->entityManager->getReference(Product::class, $cartItem->getProductId());
                $orderItem = (new OrderItem())
                    ->setProduct($product)
                    ->setQuantity($cartItem->getQuantity())
                    ->setOrder($order)
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setSubTotal($cartItem->getSubTotal());
                $this->entityManager->persist($orderItem);
            }

            $billingDetail = (new OrderBillingDetail())
                ->setOrder($order)
                ->setFullName($checkoutForm->getFullName())
                ->setAddress($checkoutForm->getAddress())
                ->setZipCode($checkoutForm->getZipCode())
                ->setCity($checkoutForm->getCity())
                ->setCountry($checkoutForm->getCountry())
                ->setCardNumber($checkoutForm->getCardNumber())
                ->setCardExpiryDate($checkoutForm->getCardExpiryDate())
                ->setCardCode($checkoutForm->getCardCode());
            $this->entityManager->persist($billingDetail);

            $this->entityManager->flush();
            
            $this->cartService->setCart(new Cart());
        } catch (\Exception $exception) {
            $this->logger->error("[\App\Modules\Storefront\Service\CustomerOrderService]");
            $this->logger->error("Error Message: {msg}", [
                'msg'   => $exception->getMessage()
            ]);
            throw new \LogicException("An error occurred while attempting to place your order...");
        }
    }
}