<?php

namespace App\Modules\Admin\Controller;

use App\Enum\SessionFlashType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
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

    #[Route('/details/{token}', name: 'details', methods: ['GET'])]
    public function details($token): RedirectResponse|Response
    {
        $customer = $this->userRepository->findOneBy([
            'token'     => $token,
        ]);
        if(is_null($customer)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The specified customer was not found..."
            );

            return $this->redirectToRoute('admin.customers.index');
        }

        $pageTitle = "Customer Details - " . $customer->getName();

        return $this->render('admin/customers/details.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'customer'          => $customer
        ]);
    }
}