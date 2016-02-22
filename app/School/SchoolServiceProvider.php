<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use Silex\Application;
use Silex\ServiceProviderInterface;

class SchoolServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['school_service'] = $app->share(
            function (Application $app) {
                return new SchoolService(
                    $app['uitpas'],
                    $app['counter_consumer_key'],
                    $app['counter_service']
                );
            }
        );
    }

    public function boot(Application $app)
    {

    }
}
