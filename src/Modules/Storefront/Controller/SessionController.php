<?php

namespace App\Modules\Storefront\Controller;

use App\Dto\Customer\CustomerSignupDto;
use App\Entity\User;
use App\Enum\SessionFlashType;
use App\Enum\UserRoleType;
use App\Utility\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/session', name: 'app.session.')]
class SessionController extends AbstractController
{
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    )
    {
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        $this->logger->info("About to Login");

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error != null) {
            $this->logger->error($error->getMessage());
            $this->addFlash(
                SessionFlashType::ERROR,
                $error->getMessage()
            );
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $pageTitle = "Login";

        return $this->render('storefront/session/login.html.twig', [
            'pageTitle'     => $pageTitle,
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request): RedirectResponse|Response
    {
        $formDto = new CustomerSignupDto();

        if($request->isMethod('POST'))
        {
            $formDto = CustomerSignupDto::fromRequest($request);

            $validationResult = $this->validator->validate($formDto);
            if(count($validationResult) > 0)
            {
                $this->addFlash(
                    SessionFlashType::ERROR,
                    "One or more errors occurred"
                );

                /** @var ConstraintViolation $violation */
                foreach ($validationResult as $violation)
                {
                    $this->addFlash(
                        SessionFlashType::ERROR,
                        $violation->getMessage()
                    );
                }
            }
            else
            {
                $user = new User();
                $user->setToken(TokenGenerator::generateUserToken())
                    ->setRoles([UserRoleType::ROLE_STORE_USER])
                    ->setName($formDto->getName())
                    ->setEmail($formDto->getEmail())
                    ->setPassword($this->passwordHasher->hashPassword($user, $formDto->getPassword()));

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash(
                    SessionFlashType::SUCCESS,
                    "Your account has been created successfully. Please proceed to login..."
                );

                return $this->redirectToRoute('app.session.login');
            }
        }

        $pageTitle = "Create An Account";

        return $this->render('storefront/session/register.html.twig', [
            'pageTitle'     => $pageTitle,
            'formDto'       => $formDto
        ]);
    }

    #[Route('/forgot-password', name: 'forgot-password', methods: ['GET', 'POST'])]
    public function forgotPassword(): RedirectResponse
    {
        $this->addFlash(
            SessionFlashType::ERROR,
            "Email services not configured..."
        );

        return $this->redirectToRoute('app.session.login');
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}