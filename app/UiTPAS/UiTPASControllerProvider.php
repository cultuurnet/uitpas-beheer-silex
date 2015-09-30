<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\RegistrationJsonDeserializer;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class UiTPASControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['uitpas_controller'] = $app->share(
            function (Application $app) {
                return new UiTPASController(
                    $app['uitpas_service'],
                    new RegistrationJsonDeserializer(
                        new KansenStatuutJsonDeserializer()
                    )
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->delete('/uitpas/{uitpasNumber}', 'uitpas_controller:block');
        $controllers->put('/uitpas/{uitpasNumber}', 'uitpas_controller:register');

        $controllers->get('/uitpas/{uitpasNumber}/price', 'uitpas_controller:getPrice');

        return $controllers;
    }
}
