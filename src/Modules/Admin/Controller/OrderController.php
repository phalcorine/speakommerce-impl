<?php

namespace App\Modules\Admin\Controller;

use App\Entity\OrderItem;
use App\Enum\OrderStatusType;
use App\Enum\SessionFlashType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders', name: 'admin.orders.')]
class OrderController extends AbstractController
{
    private string $sectionTitle = "Manage Orders";

    private OrderRepository $orderRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        OrderRepository $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
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

        /** @var OrderItem[] $orderItems */
        $orderItems = $order->getOrderItems();

        $pageTitle = "Order Details - " . $order->getToken();

        return $this->render('admin/orders/details.html.twig', [
            'sectionTitle'  => $this->sectionTitle,
            'pageTitle'     => $pageTitle,
            'order'         => $order,
            'orderItems'    => $orderItems
        ]);
    }

    #[Route('/change-status/{token}', name: 'change_status', methods: ['GET', 'POST'])]
    public function changeStatus($token, Request $request): RedirectResponse|Response
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

        if ($request->isMethod('POST'))
        {
            $status = $request->request->get('order_status');
            $order->setStatus($status);
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->addFlash(
                SessionFlashType::SUCCESS,
                "Order Status changed successfully..."
            );

            return $this->redirectToRoute('admin.orders.details', [
                'token'     => $token
            ]);
        }

        $pageTitle = "Change Status for Order #" . $order->getToken();

        return $this->render('admin/orders/change_status.html.twig', [
            'sectionTitle'  => $this->sectionTitle,
            'pageTitle'     => $pageTitle,
            'order'         => $order,
            'orderStatuses' => OrderStatusType::getOrderStatusTypes()
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(Request $request): Response
    {
        if($request->isMethod('POST'))
        {
            $token = $request->request->get('search_term');
            $order = $this->orderRepository->findOneBy([
                'token'     => $token
            ]);
            if ($order) {
                return $this->redirectToRoute('admin.orders.details', [
                    'token'     => $token
                ]);
            }

            $this->addFlash(
                SessionFlashType::ERROR,
                "The Order ID specified was not found. Please check and try again..."
            );
        }

        $pageTitle = "Search Order";

        return $this->render('admin/orders/search.html.twig', [
            'pageTitle'     => $pageTitle
        ]);
    }
}