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
        $app['activity_controller'] = $app->share(
            function (Application $app) {
                return new ActivityController(
                    $app['activity_service'],
                    $app['activity_query'],
                    $app['url_generator']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/activities', 'activity_controller:search');
        $controllers->get('/passholders/{uitpasNumber}/activities', 'activity_controller:search');

        $app['checkin_controller'] = $app->share(
            function (Application $app) {
                return new CheckinController(
                    $app['checkin_service'],
                    $app['activity_service'],
                    new CheckinCommandDeserializer()
                );
            }
        );

        $controllers->post('/passholders/{uitpasNumber}/activities/checkins', 'checkin_controller:checkin');

        return $controllers;
    }
}
