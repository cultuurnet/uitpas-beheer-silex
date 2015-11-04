<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class HelpControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['help_controller'] = $app->share(
            function (Application $app) {
                return new HelpController(
                    new FileStorage(__DIR__ . '/../../var/help.md'),
                    $app['security.authorization_checker']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/help', 'help_controller:get');
        $controllers->post('/help', 'help_controller:update');

        return $controllers;
    }
}
