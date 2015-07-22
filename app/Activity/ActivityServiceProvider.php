<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\CalendarSummary\CalendarPlainTextFormatter;
use CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS\ActivityService;
use CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS\Query;
use CultuurNet\UiTPASBeheer\Activity\SearchAPI2\SearchAPI2AugmentedActivityService;
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
                return new SearchAPI2AugmentedActivityService(
                    new ActivityService(
                        $app['uitpas'],
                        $app['counter_consumer_key']
                    ),
                    $app['cultuurnet_search'],
                    new CalendarPlainTextFormatter()
                );
            }
        );

        $app['activity_query'] = $app->share(
            function (Application $app) {
                $query = new Query($app['clock']);
                return $query->withSort('permanent desc,availableto asc');
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
