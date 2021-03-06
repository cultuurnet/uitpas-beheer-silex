<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use Auth0\SDK\Auth0;
use CultuurNet\UiTIDProvider\User\UserSessionService;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @var string[]
     */
    private $loginParameters;

    /**
     * @var string
     */
    private $redirectUrlAfterLogin;

    public function __construct(
        Auth0 $auth0,
        SessionInterface $session,
        UserSessionService $userSessionService,
        UiTIDv1TokenService $uitIDv1TokenService,
        array $loginParameters,
        string $redirectUrlAfterLogin
    ) {
        $this->auth0 = $auth0;
        $this->session = $session;
        $this->userSessionService = $userSessionService;
        $this->uitIDv1TokenService = $uitIDv1TokenService;
        $this->loginParameters = $loginParameters;
        $this->redirectUrlAfterLogin = $redirectUrlAfterLogin;
    }

    public function redirectToLoginService(): void
    {
        // Clear any persistent Auth0 data that lingers in some edge cases even if the user is considered to be logged
        // out by the Balie app. For example, when a user with only a v2 id logs in they get an error because they need
        // a v1 id to get the v1 token. The user is not logged in then according to the app, but they are according to
        // the Auth0 SDK. So calling this in the logout functionality of our own app won't fix this either. The safest
        // way is to call it right before redirecting to the Auth0 login.
        $this->auth0->logout();

        // The Auth0 SDK sets a Location header and then exits, so we do not need to return a Response object.
        $this->auth0->login(null, null, $this->loginParameters);
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

    public function getToken(): JsonResponse
    {
        $accessToken = $this->session->get('auth0_access_token', null);

        if ($accessToken === null) {
            throw new AccessTokenNotFoundException();
        }

        return new JsonResponse(['token' => $accessToken]);
    }
}
