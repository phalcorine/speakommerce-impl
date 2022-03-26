<?php

namespace App\Modules\Admin\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders', name: 'admin.orders.')]
class OrderController extends AbstractController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    #[Route('', name: 'index')]
    public function index()
    {
        $orders = $this->orderRepository->findBy([], [
            'createdAt' => 'DESC'
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search()
    {

    }
}