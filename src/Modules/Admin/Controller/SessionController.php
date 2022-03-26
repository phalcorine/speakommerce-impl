<?php

namespace App\Modules\Admin\Controller;

use App\Enum\SessionFlashType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin/session', name: 'admin.session.')]
class SessionController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error != null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                $error->getMessage()
            );
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $pageTitle = "Login";

        return $this->render('admin/session/login.html.twig', [
            'pageTitle'     => $pageTitle,
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}