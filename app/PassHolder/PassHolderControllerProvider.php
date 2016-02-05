<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Search\Query;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class PassHolderControllerProvider implements ControllerProviderInterface
{
    const EXPORT_FILENAME = 'passholders.xls';

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
                    $app['passholder_iterator_factory'],
                    $app['passholder_export_filewriter'],
                    $app['passholder_json_deserializer'],
                    $app['registration_json_deserializer'],
                    new Query(),
                    $app['url_generator'],
                    $app['counter']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders', 'passholder_controller:search');

        $controllers->get('/passholders/{uitpasNumber}', 'passholder_controller:getByUitpasNumber');
        $controllers->put('/passholders/{uitpasNumber}', 'passholder_controller:register');
        $controllers->post('/passholders/{uitpasNumber}', 'passholder_controller:update');

        $controllers->get('/' . self::EXPORT_FILENAME, 'passholder_controller:export');

        return $controllers;
    }
}
