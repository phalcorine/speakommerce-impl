<?php

namespace App\Modules\Admin\Service;

use App\Dto\Dashboard\DashboardStatisticsDto;
use App\Enum\OrderStatusType;
use App\Repository\OrderRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

class AdminDashboardService
{
    private ProductCategoryRepository $categoryRepository;
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(
        ProductCategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public function getTotalProductCategoriesCount(): int
    {
        return $this->categoryRepository->count([]);
    }

    public function getTotalProductsCount(): int
    {
        return $this->productRepository->count([]);
    }

    public function getTotalOrdersCount(): int
    {
        return $this->orderRepository->count([]);
    }

    public function getTotalPendingOrdersCount(): int
    {
        return $this->orderRepository->count([
            'status'    => OrderStatusType::PENDING
        ]);
    }

    public function getTotalCustomersCount(): int
    {
        return $this->userRepository->count([]);
    }

    public function getLatestOrders($limit = 5)
    {
        return $this->orderRepository->findBy([
            'status'    => OrderStatusType::PENDING
        ], [
            'createdAt' => 'DESC'
        ], $limit);
    }

    public function getAllStats(): DashboardStatisticsDto
    {
        return (new DashboardStatisticsDto())
            ->setTotalCategories($this->getTotalProductCategoriesCount())
            ->setTotalCustomers($this->getTotalCustomersCount())
            ->setTotalOrders($this->getTotalOrdersCount())
            ->setTotalPendingOrders($this->getTotalPendingOrdersCount())
            ->setTotalProducts($this->getTotalProductsCount());
    }
}