<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Advantage\CashIn\CashInJsonDeserializer;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AdvantageControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['advantage_controller'] = $app->share(function (Application $app) {
            $controller = new AdvantageController($app['advantage_identifier_json_deserializer']);

            $controller->registerAdvantageService($app['points_promotion_advantage_service']);
            $controller->registerAdvantageService($app['welcome_advantage_service']);

            return $controller;
        });

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders/{uitpasNumber}/advantages', 'advantage_controller:getExchangeable');
        $controllers->get('/passholders/{uitpasNumber}/advantages/{advantageIdentifier}', 'advantage_controller:get');

        $controllers->post('/passholders/{uitpasNumber}/advantages/exchanges', 'advantage_controller:exchange');

        return $controllers;
    }
}
