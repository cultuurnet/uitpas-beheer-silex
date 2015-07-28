<?php

namespace CultuurNet\UiTPASBeheer;

use CultuurNet\Search\Guzzle\Service as SearchService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CultuurNetServiceProvider implements ServiceProviderInterface
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
        $app['uitpas'] = $app->share(
            function (Application $app) {
                /* @var \CultureFeed $culturefeed */
                $culturefeed = $app['culturefeed'];
                return $culturefeed->uitpas();
            }
        );

        $app['cultuurnet_search'] = $app->share(
            function (Application $app) {
                return new SearchService(
                    $app['cultuurnet.search.endpoint'],
                    $app['culturefeed_consumer_credentials']
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
