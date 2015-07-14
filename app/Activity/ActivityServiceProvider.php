<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ActivityServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['activity_service'] = $app->share(
            function ($app) {
                return new ActivityService(
                    $app['uitpas'],
                    $app['counter_consumer_key'],
                    $app['cultuurnet_search']
                );
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
