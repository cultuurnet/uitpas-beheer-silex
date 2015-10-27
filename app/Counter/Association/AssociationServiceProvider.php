<?php

namespace CultuurNet\UiTPASBeheer\Counter\Association;

use Silex\Application;
use Silex\ServiceProviderInterface;

class AssociationServiceProvider implements ServiceProviderInterface
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

        $app['association_service'] = $app->share(
            function (Application $app) {
                return new AssociationService(
                    $app['uitpas'],
                    $app['counter_consumer_key']
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
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
