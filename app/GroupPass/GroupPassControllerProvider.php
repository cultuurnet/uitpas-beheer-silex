<?php

namespace CultuurNet\UiTPASBeheer\GroupPass;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

final class GroupPassControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app): ControllerCollection
    {
        $app['group_pass_controller'] = function (Application $app) {
            return new GroupPassController($app['uitpas']);
        };

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];
        $controllers->get('/{id}', 'group_pass_controller:getGroupPassInfo');

        return $controllers;
    }
}
