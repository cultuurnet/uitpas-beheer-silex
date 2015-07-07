<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use Silex\Application;
use Silex\ServiceProviderInterface;

class PassHolderServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['passholder_service'] = $app->share(function (Application $app) {
            return new PassHolderService(
                $app['uitpas'],
                $app['counter_consumer_key']
            );
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
