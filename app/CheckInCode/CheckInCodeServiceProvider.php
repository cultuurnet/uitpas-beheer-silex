<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use Silex\Application;
use Silex\ServiceProviderInterface;

final class CheckInCodeServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['checkin_code_service'] = $app->share(
            function (Application $app) {
                /* @var ConsumerCredentials $consumerCredentials */
                $consumerCredentials = $app['culturefeed_consumer_credentials'];

                /* @var TokenCredentials|null $tokenCredentials */
                $tokenCredentials = $app['culturefeed_token_credentials'];

                return new UiTPASV1ApiCheckInCodeService(
                    $app['counter_consumer_key'],
                    $app['culturefeed.endpoint'],
                    $consumerCredentials,
                    $tokenCredentials
                );
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
