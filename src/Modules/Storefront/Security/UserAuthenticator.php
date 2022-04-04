<?php

namespace App\Modules\Storefront\Security;

use App\Entity\AdminUser;
use App\Entity\User;
use App\Enum\SessionFlashType;
use App\Repository\AdminUserRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app.session.login';

    private UrlGeneratorInterface $urlGenerator;
    private LoggerInterface $logger;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger,
        RequestStack $requestStack,
        Security $security,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $user = $this->userRepository->findOneBy([
            'email'     => $email,
        ]);

        if(is_null($user) || !$this->isPasswordValid($user, $password)) {
            throw new CustomUserMessageAuthenticationException('Invalid Credentials. Please check and try again...');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('user_authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $name = $this->security->getUser()->getName();
        $this->requestStack->getSession()->getFlashBag()->add(
            SessionFlashType::SUCCESS,
            "Welcome back, " . $name . "..."
        );

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app.home.index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function isPasswordValid(User $user, $password): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $password);
    }
}
