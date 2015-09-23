<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class KansenStatuutControllerProvider implements ControllerProviderInterface
{
    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        $app['kansenstatuut_controller'] = $app->share(
            function (Application $app) {
                return new KansenStatuutController(
                    $app['kansenstatuut_service'],
                    new KansenStatuutEndDateJSONDeserializer()
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->post(
            '/passholders/{uitpasNumber}/kansenstatuten/{cardSystemId}',
            'kansenstatuut_controller:renew'
        );

        return $controllers;
    }
}
