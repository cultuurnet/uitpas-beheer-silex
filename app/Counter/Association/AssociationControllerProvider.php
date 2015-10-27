<?php

namespace CultuurNet\UiTPASBeheer\Counter\Association;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class AssociationControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['association_controller'] = $app->share(
            function (Application $app) {
                return new AssociationController(
                    $app['association_service']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/counters/active/associations', 'association_controller:getAssociations');

        return $controllers;
    }
}
