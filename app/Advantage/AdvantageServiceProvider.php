<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use Silex\Application;
use Silex\ServiceProviderInterface;

class AdvantageServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['advantage_identifier_json_deserializer'] = $app->share(function (Application $app) {
            return new AdvantageIdentifierJsonDeserializer();
        });

        $app['welcome_advantage_service'] = $app->share(function (Application $app) {
            return new WelcomeAdvantageService(
                $app['uitpas'],
                $app['counter_consumer_key']
            );
        });

        $app['points_promotion_advantage_service'] = $app->share(function (Application $app) {
            return new PointsPromotionAdvantageService(
                $app['uitpas'],
                $app['counter_consumer_key']
            );
        });
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
