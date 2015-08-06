<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class IdentityControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['identity_controller'] = $app->share(
            function (Application $app) {
                return new IdentityController($app['identity_service']);
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/identities/{identificationNumber}', 'identity_controller:get');

        return $controllers;
    }
}
