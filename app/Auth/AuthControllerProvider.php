<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use Auth0\SDK\Auth0;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

final class AuthControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app): ControllerCollection
    {
        $app['auth_controller'] = $app->share(
            function (Application $app) {
                return new AuthController(
                    $app[Auth0::class],
                    $app['session'],
                    $app['uitid_user_session_service'],
                    $app[UiTIDv1TokenService::class],
                    $app['config']['auth0']['login_parameters'],
                    $app['config']['auth0']['app_url']
                );
            }
        );

        $controllers = $app['controllers_factory'];

        $controllers->get('/connect', 'auth_controller:redirectToLoginService');
        $controllers->get('/authorize', 'auth_controller:storeTokenAndRedirectToFrontend');
        $controllers->get('/token', 'auth_controller:getToken');

        return $controllers;
    }
}
