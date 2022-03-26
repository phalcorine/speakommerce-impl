<?php

namespace App\Modules\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/product-categories', name: 'admin.product_categories.')]
class ProductCategoryController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index()
    {

    }
}