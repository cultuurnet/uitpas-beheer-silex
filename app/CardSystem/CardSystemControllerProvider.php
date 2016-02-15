<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CardSystemControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['cardsystem_controller'] = $app->share(
            function (Application $app) {
                return new CardSystemController(
                    $app['cardsystem_service']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/cardsystem/{cardSystemId}/price', 'cardsystem_controller:getPrice');

        return $controllers;
    }
}
