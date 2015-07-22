<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer;

use CultuurNet\Clock\SystemClock;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ClockServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['clock'] = $app->share(
            function (Application $app) {
                $timeZoneIdentifier = $app['clock.timezone'];
                if (!$timeZoneIdentifier) {
                    $timeZoneIdentifier = date_default_timezone_get();
                }

                $timeZone = new \DateTimeZone($timeZoneIdentifier);

                return new SystemClock($timeZone);
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
