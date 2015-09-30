<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class UiTPASControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['uitpas_controller'] = $app->share(
            function (Application $app) {
                return new UiTPASController(
                    $app['uitpas_service']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->delete('/uitpas/{uitpasNumber}', 'uitpas_controller:block');

        $controllers->get('/uitpas/{uitpasNumber}/price', 'uitpas_controller:getPrice');

        return $controllers;
    }
}
