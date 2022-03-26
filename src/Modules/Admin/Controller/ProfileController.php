<?php

namespace App\Modules\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/profile', name: 'admin.profile.')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index()
    {

    }
}