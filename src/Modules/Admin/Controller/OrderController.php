<?php

namespace App\Modules\Admin\Controller;

use App\Enum\SessionFlashType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders', name: 'admin.orders.')]
class OrderController extends AbstractController
{
    private string $sectionTitle = "Manage Orders";

    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $orders = $this->orderRepository->findBy([], [
            'createdAt' => 'DESC'
        ]);

        $pageTitle = "List of Orders";

        return $this->render('admin/orders/index.html.twig', [
            'sectionTitle'  => $this->sectionTitle,
            'pageTitle'     => $pageTitle,
            'orders'        => $orders
        ]);
    }

    #[Route('/details/{token}', name: 'details')]
    public function details($token): RedirectResponse|Response
    {
        $order = $this->orderRepository->findOneBy([
            'token'     => $token
        ]);
        if(is_null($order)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The Order specified does not exists..."
            );

            return $this->redirectToRoute('admin.orders.index');
        }

        $pageTitle = "Order Details - " . $order->getToken();

        return $this->render('admin/orders/details.html.twig', [
            'sectionTitle'  => $this->sectionTitle,
            'pageTitle'     => $pageTitle,
            'order'         => $order
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search()
    {

    }
}