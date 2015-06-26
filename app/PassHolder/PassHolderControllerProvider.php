<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class PassHolderControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['passholder_controller'] = $app->share(function (Application $app) {
            return new PassHolderController($app['passholder_service']);
        });

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholder', 'passholder_controller:getByIdentificationNumber');

        return $controllers;
    }
}
