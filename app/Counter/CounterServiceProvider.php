<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Silex\Application;
use Silex\ServiceProviderInterface;

class CounterServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['uitpas_counter_service'] = $app->share(
            function ($app) {
                return new CounterService(
                    $app['session'],
                    $app['culturefeed_uitpas'],
                    $app['uitid_current_user']
                );
            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
