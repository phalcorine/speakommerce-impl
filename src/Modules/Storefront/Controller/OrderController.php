<?php

namespace App\Modules\Storefront\Controller;

use App\Enum\SessionFlashType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'app.orders.')]
class OrderController extends AbstractController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $orders = $this->orderRepository->findBy([
            'user'      => $this->getUser()
        ]);

        $pageTitle = "My Orders";

        return $this->render('storefront/orders/index.html.twig', [
            'pageTitle'     => $pageTitle,
            'orders'        => $orders
        ]);
    }

    #[Route('/details/{token}', name: 'details', methods: ['GET', 'POST'])]
    public function details(string $token)
    {
        $order = $this->orderRepository->findOneBy([
            'token'     => $token,
            'user'      => $this->getUser()
        ]);
        if(is_null($order)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The order specified was not found..."
            );

            return $this->redirectToRoute('app.orders.index');
        }

        $pageTitle = "Order Details - #" . $order->getToken();

        return $this->render('storefront/orders/details.html.twig', [
            'pageTitle'     => $pageTitle,
            'order'         => $order
        ]);

    }
}