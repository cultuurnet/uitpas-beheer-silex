<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use Silex\Application;
use Silex\ServiceProviderInterface;

class KansenStatuutServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['kansenstatuut_service'] = $app->share(
            function (Application $app) {
                return new KansenStatuutService(
                    $app['uitpas'],
                    $app['counter_consumer_key']
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
