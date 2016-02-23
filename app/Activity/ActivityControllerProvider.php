<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\RegistrationJsonDeserializer;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\TicketSaleController;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ActivityControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app An Application instance
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $app['activity_controller'] = $app->share(
            function (Application $app) {
                return new ActivityController(
                    $app['activity_service'],
                    $app['activity_query'],
                    $app['url_generator']
                );
            }
        );

        $app['checkin_controller'] = $app->share(
            function (Application $app) {
                return new CheckinController(
                    $app['checkin_service'],
                    $app['activity_service'],
                    new CheckinCommandDeserializer()
                );
            }
        );

        $app['ticketsale_controller'] = $app->share(
            function (Application $app) {
                return new TicketSaleController(
                    $app['ticketsale_service'],
                    new RegistrationJsonDeserializer()
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders/{uitpasNumber}/activities', 'activity_controller:search');

        $controllers->get('/counter/active/activities', 'activity_controller:search');

        $controllers->post('/passholders/{uitpasNumber}/activities/checkins', 'checkin_controller:checkin');

        $controllers->get(
            '/passholders/{uitpasNumber}/activities/ticket-sales',
            'ticketsale_controller:getByUiTPASNumber'
        );
        $controllers->post('/passholders/{uitpasNumber}/activities/ticket-sales', 'ticketsale_controller:register');
        $controllers->delete('/passholders/{uitpasNumber}/activities/ticket-sales/{ticketSaleId}', 'ticketsale_controller:cancel');

        return $controllers;
    }
}
