<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class DataValidationControllerProvider implements ControllerProviderInterface
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
        $app['datavalidation_controller'] = $app->share(
            function (Application $app) {
                return new DataValidationController($app['datavalidation_client'], $app['validator']);
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];
        $controllers->get('/datavalidation/email', 'datavalidation_controller:validateEmail');

        return $controllers;
    }
}
