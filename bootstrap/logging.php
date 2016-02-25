<?php
/**
 * @file
 */

/** @var \Silex\Application $app */

$app['third_party_api_log'] = $app->share(
    function () {
        return new \Monolog\Handler\StreamHandler(
            __DIR__ . '/../log/third_party_api.log'
        );
    }
);

$app['third_party_api_logger_factory'] = $app->protect(
    function ($name) use ($app) {
        $logger = new Monolog\Logger($name);
        $logger->pushHandler(
            $app['third_party_api_log'],
            \Monolog\Logger::DEBUG
        );

        return $logger;
    }
);

$app['cultuurnet_search'] = $app->share(
    $app->extend(
        'cultuurnet_search',
        function (\CultuurNet\Search\Guzzle\Service $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('cultuurnet_search');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                \Guzzle\Log\MessageFormatter::DEBUG_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);

$app['uitid_auth_service'] = $app->share(
    $app->extend(
        'uitid_auth_service',
        function (\CultuurNet\Auth\Guzzle\Service $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('cultuurnet_auth');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                \Guzzle\Log\MessageFormatter::DEBUG_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);

/**
 * Enable loggin on the guzzle client
 */
$app['httpclient_guzzle'] = $app->share(
    $app->extend(
        'httpclient_guzzle',
        function (\Guzzle\Http\Client $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('culturefeed');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                \Guzzle\Log\MessageFormatter::DEBUG_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);

$app['expense_report_api'] = $app->share(
    $app->extend(
        'expense_report_api',
        function (\CultuurNet\UiTPASBeheer\ExpenseReport\ExpenseReportApiService $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('expense_report_api');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                \Guzzle\Log\MessageFormatter::DEBUG_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);
