<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MemberServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['member_service'] = $app->share(
            function (Application $app) {
                return new MemberService(
                    $app['uitpas'],
                    $app['culturefeed_oauth_client'],
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
