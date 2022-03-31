<?php

namespace App\Dto\Dashboard;

class DashboardStatisticsDto
{
    private int $totalCategories = 0;

    private int $totalProducts = 0;

    private int $totalOrders = 0;

    private int $totalPendingOrders = 0;

    private int $totalCustomers = 0;

    private int $totalSalesToday = 0;

    /**
     * @return int
     */
    public function getTotalCategories(): int
    {
        return $this->totalCategories;
    }

    /**
     * @param int $totalCategories
     * @return DashboardStatisticsDto
     */
    public function setTotalCategories(int $totalCategories): DashboardStatisticsDto
    {
        $this->totalCategories = $totalCategories;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalProducts(): int
    {
        return $this->totalProducts;
    }

    /**
     * @param int $totalProducts
     * @return DashboardStatisticsDto
     */
    public function setTotalProducts(int $totalProducts): DashboardStatisticsDto
    {
        $this->totalProducts = $totalProducts;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalOrders(): int
    {
        return $this->totalOrders;
    }

    /**
     * @param int $totalOrders
     * @return DashboardStatisticsDto
     */
    public function setTotalOrders(int $totalOrders): DashboardStatisticsDto
    {
        $this->totalOrders = $totalOrders;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPendingOrders(): int
    {
        return $this->totalPendingOrders;
    }

    /**
     * @param int $totalPendingOrders
     * @return DashboardStatisticsDto
     */
    public function setTotalPendingOrders(int $totalPendingOrders): DashboardStatisticsDto
    {
        $this->totalPendingOrders = $totalPendingOrders;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCustomers(): int
    {
        return $this->totalCustomers;
    }

    /**
     * @param int $totalCustomers
     * @return DashboardStatisticsDto
     */
    public function setTotalCustomers(int $totalCustomers): DashboardStatisticsDto
    {
        $this->totalCustomers = $totalCustomers;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalSalesToday(): int
    {
        return $this->totalSalesToday;
    }

    /**
     * @param int $totalSalesToday
     * @return DashboardStatisticsDto
     */
    public function setTotalSalesToday(int $totalSalesToday): DashboardStatisticsDto
    {
        $this->totalSalesToday = $totalSalesToday;
        return $this;
    }
}