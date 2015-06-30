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
        $app['counter_controller'] = $app->share(function (Application $app) {
            return new CounterController($app['counter_service']);
        });

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/counter/list', 'counter_controller:getCounters');

        $controllers->get('/counter/active', 'counter_controller:getActiveCounter');
        $controllers->post('/counter/active', 'counter_controller:setActiveCounter');

        return $controllers;
    }
}
