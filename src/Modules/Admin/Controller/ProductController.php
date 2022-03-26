<?php

namespace App\Modules\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products', name: 'admin.products.')]
class ProductController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index()
    {

    }
}