<?php
namespace CultuurNet\UiTPASBeheer;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UiTPASServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['uitpas'] = $app->share(
            function ($app) {
                /* @var \CultureFeed $culturefeed */
                $culturefeed = $app['culturefeed'];
                return $culturefeed->uitpas();
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
