<?php

namespace App\Modules\Admin\Controller;

use App\Modules\Admin\Service\AdminDashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/admin', name: 'admin.home.')]
#[Route('/admin', name: 'admin.dashboard.')]
class DashboardController extends AbstractController
{
    private string $sectionTitle = "Dashboard";

    private AdminDashboardService $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $dashboardStatsDto = $this->dashboardService->getAllStats();
        $latestOrders = $this->dashboardService->getLatestOrders(5);

        $pageTitle = "Home";

        return $this->render('admin/dashboard/index.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'statisticsDto'     => $dashboardStatsDto,
            'latestOrders'      => $latestOrders
        ]);
    }
}