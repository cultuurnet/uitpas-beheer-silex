<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ServiceProviderInterface;

class UiTPASServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['uitpas_service'] = $app->share(
            function (Application $app) {
                return new UiTPASService(
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
