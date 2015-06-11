<?php

namespace CultuurNet\UiTPASBeheer\Session;

use Silex\Application;
use CultuurNet\UiTIDProvider\Session\UserSessionServiceProvider as UiTIDUserSessionServiceProvider;

class UserSessionServiceProvider extends UiTIDUserSessionServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        $app['session'] = $app->share(function ($app) {
            if (!isset($app['session.storage'])) {
                if ($app['session.test']) {
                    $app['session.storage'] = $app['session.storage.test'];
                } else {
                    $app['session.storage'] = $app['session.storage.native'];
                }
            }
            return new UserSession($app['session.storage']);
        });
    }
}
