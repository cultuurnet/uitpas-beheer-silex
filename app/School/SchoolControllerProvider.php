<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class SchoolControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['school_controller'] = $app->share(
            function (Application $app) {
                return new SchoolController(
                    $app['school_service']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('schools', 'school_controller:getSchools');

        return $controllers;
    }
}
