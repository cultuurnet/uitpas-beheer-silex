<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use Silex\Application;
use Silex\ServiceProviderInterface;

class CheckInDeviceServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['check_in_device_service'] = $app->share(
            function (Application $app) {
                return new CheckInDeviceService(
                    $app['uitpas'],
                    $app['counter_consumer_key'],
                    $app['clock']
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {

    }
}
