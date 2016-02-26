<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer;

use Silex\Application;
use Silex\ServiceProviderInterface;

class CulturefeedGuzzleServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['culturefeed_oauth_client'] = $app->share(
            $app->extend(
                'culturefeed_oauth_client',
                function (\CultureFeed_DefaultOAuthClient $OAuthClient, Application $app) {
                    $OAuthClient->setHttpClient($app['culturefeed_http_client']);

                    return $OAuthClient;
                }
            )
        );

        /**
         * Culturefeed HTTP Client adapter for a Guzzle HTTP client.
         */
        $app['culturefeed_http_client'] = $app->share(
            function (\Silex\Application $app) {
                $httpClient = new \CultuurNet\CulturefeedHttpGuzzle\HttpClient(
                    $app['culturefeed_http_client_guzzle']
                );
                if (isset($app['config']['httpclient']) && isset($app['config']['httpclient']['timeout'])) {
                    $httpClientTimeOut = $app['config']['httpclient']['timeout'];
                } else {
                    $httpClientTimeOut = 30;
                }
                $httpClient->setTimeout($httpClientTimeOut);

                return $httpClient;
            }
        );

        /**
         * Guzzle HTTP client.
         */
        $app['culturefeed_http_client_guzzle'] = $app->share(
            function () {
                return new \Guzzle\Http\Client();
            }
        );
    }

    public function boot(Application $app)
    {

    }
}
