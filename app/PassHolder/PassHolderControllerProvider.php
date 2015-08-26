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
        $controllers->put('/passholders/{uitpasNumber}', 'passholder_controller:register');

        // @todo Use PATCH instead of POST. See UBR-179.
        $controllers->post('/passholders/{uitpasNumber}', 'passholder_controller:update');

        $controllers->get('/uitpas-prices', 'passholder_controller:getPrices');

        return $controllers;
    }
}
