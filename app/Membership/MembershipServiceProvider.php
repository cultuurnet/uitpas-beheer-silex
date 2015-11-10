<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Membership\Registration\RegistrationJsonDeserializer;
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

        $app['membership_registration_deserializer'] = $app->share(
            function (Application $app) {
                return new RegistrationJsonDeserializer();
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
