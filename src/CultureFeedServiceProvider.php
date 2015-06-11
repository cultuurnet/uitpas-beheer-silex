<?php
namespace CultuurNet\UiTPASBeheer;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UiTIDProvider\Session\UserSession;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CultureFeedServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['culturefeed_token_credentials'] = $app->share(
            function ($app) {
                /* @var UserSession $session */
                $session = $app['session'];
                $user = $session->getUser();
                if (!is_null($user)) {
                    return $user->getTokenCredentials();
                } else {
                    return null;
                }
            }
        );

        $app['culturefeed_consumer_credentials'] = $app->share(
            function ($app) {
                return new ConsumerCredentials(
                    $app['culturefeed.consumer.key'],
                    $app['culturefeed.consumer.secret']
                );
            }
        );

        $app['culturefeed'] = $app->share(
            function ($app) {
                /* @var ConsumerCredentials $consumerCredentials */
                $consumerCredentials = $app['culturefeed_consumer_credentials'];

                /* @var TokenCredentials $tokenCredentials */
                $tokenCredentials = $app['culturefeed_token_credentials'];

                if (!is_null($tokenCredentials)) {
                    $tokenCredentialsToken = $tokenCredentials->getToken();
                    $tokenCredentialsSecret = $tokenCredentials->getSecret();
                } else {
                    $tokenCredentialsToken = null;
                    $tokenCredentialsSecret = null;
                }

                $oathClient = new \CultureFeed_DefaultOAuthClient(
                    $consumerCredentials->getKey(),
                    $consumerCredentials->getSecret(),
                    $tokenCredentialsToken,
                    $tokenCredentialsSecret
                );
                $oathClient->setEndpoint($app['culturefeed.endpoint']);

                return new \CultureFeed($oathClient);
            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
