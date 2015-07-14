<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS\ActivityService;
use CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS\Query;
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
            function (Application $app) {
                return new ActivityService(
                    $app['uitpas'],
                    $app['counter_consumer_key'],
                    $app['cultuurnet_search']
                );
            }
        );

        $app['activity_query'] = $app->share(
            function (Application $app) {
                return new Query($app['clock']);
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
