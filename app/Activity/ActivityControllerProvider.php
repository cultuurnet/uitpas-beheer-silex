<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ActivityControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $app['activity_controller'] = $app->share(function (Application $app) {
            return new ActivityController(
                $app['activity_service'],
                $app['activity_query'],
                $app['url_generator']
            );
        });

        $controllers->get('/activities', 'activity_controller:search');

        $app['checkin_controller'] = $app->share(function (Application $app) {
            return new CheckinController(
                $app['checkin_service']
            );
        });

        $controllers->post('/passholders/{uitpasNumber}/activities/checkins', 'checkin_controller:checkin');

        return $controllers;
    }
}
