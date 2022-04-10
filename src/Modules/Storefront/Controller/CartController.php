<?php

namespace App\Modules\Storefront\Controller;

use App\Enum\SessionFlashType;
use App\Modules\Storefront\Model\CheckoutForm;
use App\Modules\Storefront\Service\CustomerOrderService;
use App\Modules\Storefront\Service\SessionCartService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/cart', name: 'app.cart.')]
class CartController extends AbstractController
{
    private SessionCartService $cartService;
    private ValidatorInterface $validator;

    public function __construct(
        ValidatorInterface $validator,
        SessionCartService $cartService
    )
    {
        $this->cartService = $cartService;
        $this->validator = $validator;
    }

    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $cart = $this->cartService->getCart();

        $pageTitle = "Cart";

        return $this->render('storefront/cart/cart.html.twig', [
            'cart'      => $cart,
            'pageTitle' => $pageTitle
        ]);
    }

    #[Route('/checkout', name: 'checkout', methods: ['GET', 'POST'])]
    public function checkout(Request $request, CustomerOrderService $orderService): Response
    {
        if(!$this->isGranted('ROLE_STORE_USER'))
        {
            $this->addFlash(
                SessionFlashType::ERROR,
                "You need to login to access this page..."
            );

            return $this->redirectToRoute('app.session.login');
        }

        $cart = $this->cartService->getCart();
        if(count($cart->getItems()) <= 0) {
            $this->addFlash(
                SessionFlashType::NOTICE,
                "You can not proceed to checkout as you have no items in your cart..."
            );

            return $this->redirectToRoute('app.home.index');
        }

        $form = new CheckoutForm();

        if($request->isMethod('POST'))
        {
            $form = CheckoutForm::fromRequest($request);

            try {
                $orderService->placeOrder($this->getUser()->getId(), $form);

                $this->addFlash(
                    SessionFlashType::SUCCESS,
                    "Your order has been placed successfully..."
                );

                return $this->redirectToRoute('app.cart.order_confirmed');
            } catch (\Exception $exception) {
                $this->addFlash(
                    SessionFlashType::ERROR,
                    $exception->getMessage()
                );
            }
        }

        $pageTitle = "Checkout";

        return $this->render('storefront/cart/checkout.html.twig', [
            'pageTitle'     => $pageTitle,
            'formDto'       => $form
        ]);
    }

    #[Route('/order-confirmation', name: 'order_confirmed', methods: ['GET'])]
    public function orderConfirmed(Request $request): Response
    {
        $pageTitle = "Order Confirmed";

        $orderMessage = "Your order has been placed successfully...";

        return $this->render('storefront/cart/order_confirmed.html.twig', [
            'pageTitle'     => $pageTitle,
            'orderMessage'  => $orderMessage
        ]);
    }


}