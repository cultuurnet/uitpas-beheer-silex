<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use Auth0\SDK\Auth0;
use CultuurNet\UiTIDProvider\User\UserSessionService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class AuthController
{
    /**
     * @var Auth0
     */
    private $auth0;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var UserSessionService
     */
    private $userSessionService;

    /**
     * @var UiTIDv1TokenService
     */
    private $uitIDv1TokenService;

    /**
     * @var string
     */
    private $redirectUrlAfterLogin;

    public function __construct(
        Auth0 $auth0,
        SessionInterface $session,
        UserSessionService $userSessionService,
        UiTIDv1TokenService $uitIDv1TokenService,
        string $redirectUrlAfterLogin
    ) {
        $this->auth0 = $auth0;
        $this->session = $session;
        $this->userSessionService = $userSessionService;
        $this->uitIDv1TokenService = $uitIDv1TokenService;
        $this->redirectUrlAfterLogin = $redirectUrlAfterLogin;
    }

    public function redirectToLoginService(): void
    {
        // The Balie app is not multilingual, so locale can always be NL.
        // The ui_type=minimal parameter is needed to show a simple login screen without social logins etc.
        // The prompt=login parameter is needed to always force the user to login again, even if the user is still
        // technically logged in on Auth0.
        $params = [
            'locale' => 'nl',
            'ui_type' => 'minimal',
            'prompt' => 'login',
        ];

        // The Auth0 SDK sets a Location header and then exits, so we do not need to return a Response object.
        $this->auth0->login(null, null, $params);
    }

    public function storeTokenAndRedirectToFrontend(): RedirectResponse
    {
        $accessToken = $this->auth0->getAccessToken();
        $uitIDv1Token = $this->uitIDv1TokenService->getV1TokenForAuth0AccessToken($accessToken);

        // Store the Auth0 access token so the frontend can request it later to access the Balie Insights API.
        $this->session->set('auth0_access_token', $accessToken);

        // Store the v1 token and user id in the pre-existing UserSessionService so the rest of the app keeps working as
        // before.
        $this->userSessionService->setMinimalUserInfo($uitIDv1Token);

        return new RedirectResponse($this->redirectUrlAfterLogin);
    }
}
