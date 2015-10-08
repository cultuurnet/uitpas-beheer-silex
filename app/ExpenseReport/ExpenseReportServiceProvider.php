<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ExpenseReportServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['expense_report_service'] = $app->share(
            function (Application $app) {
                return new ExpenseReportService(
                    $app['uitpas'],
                    $app['counter_consumer_key'],
                    $app['url_generator']
                );
            }
        );

        $app['expense_report_api'] = $app->share(
            function (Application $app) {
                /* @var ConsumerCredentials $consumerCredentials */
                $consumerCredentials = $app['culturefeed_consumer_credentials'];

                /* @var TokenCredentials|null $tokenCredentials */
                $tokenCredentials = $app['culturefeed_token_credentials'];

                return new ExpenseReportApiService(
                    $app['counter_consumer_key'],
                    $app['culturefeed.endpoint'],
                    $consumerCredentials,
                    $tokenCredentials
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
