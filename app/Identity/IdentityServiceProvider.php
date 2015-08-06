<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use Silex\Application;
use Silex\ServiceProviderInterface;

class IdentityServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['identity_service'] = $app->share(
            function (Application $app) {
                return new IdentityService(
                    $app['uitpas'],
                    $app['counter_consumer_key']
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
