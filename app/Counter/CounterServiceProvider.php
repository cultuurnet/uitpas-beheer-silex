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
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['counter_service'] = $app->share(
            function ($app) {
                return new CounterService(
                    $app['session'],
                    $app['uitpas'],
                    $app['uitid_user']
                );
            }
        );

        $app['counter_consumer_key'] = $app->share(function (Application $app) {
            /* @var CounterService $counterService */
            $counterService = $app['counter_service'];

            try {
                $counter = $counterService->getActiveCounter();
                return new CounterConsumerKey($counter->consumerKey);
            } catch (CounterNotSetException $exception) {
                return null;
            }
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
