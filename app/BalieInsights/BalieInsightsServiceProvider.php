<?php
/**
 * @file
 * Provides services for balie insights.
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Silex\Application;
use Silex\ServiceProviderInterface;

class BalieInsightsServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['balie_insights_guzzle'] = $app->share(
            function (\Silex\Application $app) {
                return new \Guzzle\Http\Client($app['config']['balie_insights']['base_url']);
            }
        );

        $app['balie_insights_service'] = $app->share(
            function (Application $app) {
                return new BalieInsightsService(
                    $app['balie_insights_guzzle']
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
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
