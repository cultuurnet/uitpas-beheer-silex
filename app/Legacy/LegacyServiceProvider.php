<?php

namespace CultuurNet\UiTPASBeheer\Legacy;

use CultuurNet\UiTPASBeheer\Legacy\PassHolder\LegacyPassHolderService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class LegacyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['legacy_passholder_service'] = $app->share(
            function (Application $app) {
                return new LegacyPassHolderService(
                    $app['uitpas'],
                    $app['counter_consumer_key']
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
