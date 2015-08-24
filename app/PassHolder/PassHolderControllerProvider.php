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
        $app['passholder_controller'] = $app->share(
            function (Application $app) {
                return new PassHolderController(
                    $app['passholder_service'],
                    $app['passholder_json_deserializer'],
                    $app['registration_json_deserializer']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders/{uitpasNumber}', 'passholder_controller:getByUitpasNumber');
        $controllers->post('/passholders/{uitpasNumber}', 'passholder_controller:update');
        $controllers->post('/passholders', 'passholder_controller:register');

        $controllers->get('/uitpas-offers', 'passholder_controller:getOffers');

        return $controllers;
    }
}
