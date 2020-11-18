<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CheckInCodeControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['checkin_code_controller'] = $app->share(
            function (Application $app) {
                return new CheckInCodeController(
                    $app['checkin_code_service']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/checkincodes/{activityId}', 'checkin_code_controller:download');

        return $controllers;
    }
}
