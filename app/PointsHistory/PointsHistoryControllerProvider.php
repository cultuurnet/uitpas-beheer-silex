<?php

namespace CultuurNet\UiTPASBeheer\PointsHistory;

use CultuurNet\UiTPASBeheer\PointsTransaction\PointsTransactionController;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class PointsHistoryControllerProvider implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $app['pointshistory_controller'] = $app->share(
            function (Application $app) {
                return new PointsTransactionController($app['pointshistory_service'], $app['clock']);
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders/{uitpasNumber}/points-history', 'pointshistory_controller:search');

        return $controllers;
    }
}
