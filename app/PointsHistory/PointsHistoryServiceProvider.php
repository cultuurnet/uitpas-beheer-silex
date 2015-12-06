<?php

namespace CultuurNet\UiTPASBeheer\PointsHistory;

use CultuurNet\UiTPASBeheer\PointsTransaction\CashedPromotionPointsTransactionService;
use CultuurNet\UiTPASBeheer\PointsTransaction\CheckinPointsTransactionService;
use CultuurNet\UiTPASBeheer\PointsTransaction\CombinedPointsTransactionService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PointsHistoryServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['cashed_promotion_points_transaction_service'] = $app->share(
            function (Application $app) {
                return new CashedPromotionPointsTransactionService(
                    $app['uitpas'],
                    $app['counter_consumer_key']
                );
            }
        );

        $app['checkin_points_transaction_service'] = $app->share(
            function (Application $app) {
                return new CheckinPointsTransactionService(
                    $app['uitpas'],
                    $app['counter_consumer_key']
                );
            }
        );

        $app['pointshistory_service'] = $app->share(
            function (Application $app) {
                return new CombinedPointsTransactionService(
                    $app['cashed_promotion_points_transaction_service'],
                    $app['checkin_points_transaction_service']
                );


            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
