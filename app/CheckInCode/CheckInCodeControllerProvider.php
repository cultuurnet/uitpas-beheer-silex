<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

final class CheckInCodeControllerProvider implements ControllerProviderInterface
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

        $controllers->get('/checkincodes/{activityId}/{fileName}.pdf', 'checkin_code_controller:downloadPdf');
        $controllers->get('/checkincodes/{activityId}/{fileName}.zip', 'checkin_code_controller:downloadZip');

        return $controllers;
    }
}
