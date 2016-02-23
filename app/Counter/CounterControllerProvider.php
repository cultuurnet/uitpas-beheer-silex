<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CounterControllerProvider implements ControllerProviderInterface
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
        $app['counter_controller'] = $app->share(
            function (Application $app) {
                return new CounterController(
                    $app['counter_service'],
                    new CounterIDJsonDeserializer()
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/counters', 'counter_controller:getCounters');

        $controllers->get('/counters/active', 'counter_controller:getActiveCounter');
        $controllers->post('/counters/active', 'counter_controller:setActiveCounter');

        return $controllers;
    }
}
