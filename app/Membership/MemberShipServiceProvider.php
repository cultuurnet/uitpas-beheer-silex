<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MembershipServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['membership_service'] = $app->share(
            function (Application $app) {
                return new MembershipService(
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
