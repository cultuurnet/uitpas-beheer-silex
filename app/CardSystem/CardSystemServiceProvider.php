<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use Silex\Application;
use Silex\ServiceProviderInterface;

class CardSystemServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['cardsystem_service'] = $app->share(
            function (Application $app) {
                return new CardSystemService(
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
