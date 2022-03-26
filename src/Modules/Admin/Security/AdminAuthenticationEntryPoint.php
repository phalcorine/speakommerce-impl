<?php


namespace App\Modules\Admin\Security;


use App\Enum\SessionFlashType;
use App\Repository\AdminUserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AdminAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            SessionFlashType::ERROR,
            'You need to login to access this page...'
        );

        return new RedirectResponse($this->urlGenerator->generate('admin.session.login'));
    }
}