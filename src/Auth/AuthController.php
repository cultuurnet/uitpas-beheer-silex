<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use Auth0\SDK\Auth0;
use CultuurNet\UiTIDProvider\User\UserSessionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function redirectToLoginService(Request $request): RedirectResponse
    {
        // Clear any persistent Auth0 data that lingers in some edge cases even if the user is considered to be logged
        // out by the Balie app. For example, when a user with only a v2 id logs in they get an error because they need
        // a v1 id to get the v1 token. The user is not logged in then according to the app, but they are according to
        // the Auth0 SDK. So calling this in the logout functionality of our own app won't fix this either. The safest
        // way is to call it right before redirecting to the Auth0 login.
        $this->auth0->logout();

        // If a destination query parameter is given, save it in the session to redirect back to later after the user
        // has logged in on Auth0 and has been redirected back to the Silex backend. But only if the destination starts
        // with the configured base URL that would normally be redirected to!
        // For example configured app URL is https://balie.uitpas.be/app/ and the destination is
        // https://balie.uitpas.be/app/activities, that is okay. But if the destination is e.g. https://www.google.com
        // ignore it to prevent phishing attacks.
        $destination = $request->query->get('destination', null);
        if ($destination !== null && strpos($destination, $this->redirectUrlAfterLogin) === 0) {
            $this->session->set('auth_destination', $destination);
        }

        return new RedirectResponse($this->auth0->login(null, null, $this->loginParameters));
    }

    public function storeTokenAndRedirectToFrontend(): RedirectResponse
    {
        $this->auth0->exchange();

        $accessToken = $this->auth0->getAccessToken();
        $uitIDv1Token = $this->uitIDv1TokenService->getV1TokenForAuth0AccessToken($accessToken);

        // Store the Auth0 access token so the frontend can request it later to access the Balie Insights API.
        $this->session->set('auth0_access_token', $accessToken);

        // Store the v1 token and user id in the pre-existing UserSessionService so the rest of the app keeps working as
        // before.
        $this->userSessionService->setMinimalUserInfo($uitIDv1Token);

        // Redirect either to the app URL in the config file, or the destination query parameter that was given in the
        // login request and stored in the session in redirectToLoginService().
        $destination = $this->redirectUrlAfterLogin;
        $destinationInSession = $this->session->get('auth_destination', null);
        if ($destinationInSession) {
            $destination = $destinationInSession;
            $this->session->remove('auth_destination');
        }

        return new RedirectResponse($destination);
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
