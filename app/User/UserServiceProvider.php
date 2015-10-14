<?php

namespace CultuurNet\UiTPASBeheer\User;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['user_service'] = $app->share(
            function (Application $app) {
                return new UserService(
                    $app['culturefeed'],
                    $app['counter_consumer_key']
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {
    }
}
