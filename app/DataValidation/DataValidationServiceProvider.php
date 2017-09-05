<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use Guzzle\Http\Client;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class DataValidationServiceProvider
 *  The DataValidation service provider.
 */
class DataValidationServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['datavalidation_guzzle_client'] = $app->share(
            function (Application $app) {
                return new Client($app['data_validation.base_url']);
            }
        );

        $app['datavalidation_client'] = $app->share(
            function (Application $app) {
                return new DataValidationClient($app['datavalidation_guzzle_client'], $app['data_validation.api_key']);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
