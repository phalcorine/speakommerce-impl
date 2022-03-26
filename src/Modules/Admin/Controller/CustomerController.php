<?php

namespace App\Modules\Admin\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/customers', name: 'admin.customers.')]
class CustomerController extends AbstractController
{
    private $sectionTitle = "Customers";

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('', name: 'index')]
    public function list()
    {
        $customers = $this->userRepository->findBy([], [
            'createdAt'     => 'ASC'
        ]);

        $pageTitle = "Customer List";

        return $this->render('admin/customers/list.html.twig', [
            'sectionTitle'  => $this->sectionTitle,
            'pageTitle'     => $pageTitle,
            'customers'     => $customers
        ]);
    }
}