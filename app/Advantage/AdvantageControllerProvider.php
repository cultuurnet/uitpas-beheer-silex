<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Advantage\CashIn\CashInJsonDeserializer;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class AdvantageControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['advantage_controller'] = $app->share(function (Application $app) {
            $cashInJsonDeserializer = new CashInJsonDeserializer();
            $controller = new AdvantageController($cashInJsonDeserializer);

            $controller->registerAdvantageService($app['points_promotion_advantage_service']);
            $controller->registerAdvantageService($app['welcome_advantage_service']);

            return $controller;
        });

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders/{uitpasNumber}/advantages/cashable', 'advantage_controller:getCashable');

        $controllers->post('/passholders/{uitpasNumber}/advantages/cashed', 'advantage_controller:cashIn');

        return $controllers;
    }
}
