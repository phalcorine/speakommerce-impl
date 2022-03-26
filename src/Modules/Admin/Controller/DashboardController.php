<?php

namespace App\Modules\Admin\Controller;

use App\Modules\Admin\Service\AdminDashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin.home.')]
#[Route('/admin/dashboard', name: 'admin.dashboard.')]
class DashboardController extends AbstractController
{
    private AdminDashboardService $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    #[Route('', name: 'index')]
    public function index()
    {

    }
}