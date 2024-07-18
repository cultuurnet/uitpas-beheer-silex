<?php
/**
 * @file
 */

/** @var \Silex\Application $app */

const MESSAGE_FORMAT = ">>>>>>>>\n{request}\n<<<<<<<<\n{response}\nTime: {total_time}s\n--------\n{curl_stderr}";

$app['third_party_api_log'] = $app::share(
    function () {
        $handler = new \Monolog\Handler\StreamHandler(
            __DIR__ . '/../log/third_party_api.log'
        );

        $handler->setLevel(\Monolog\Logger::DEBUG);

        return $handler;
    }
);

$app['third_party_api_logger_factory'] = $app::protect(
    function ($name) use ($app) {
        $logger = new Monolog\Logger($name);
        $logger->pushHandler(
            $app['third_party_api_log']
        );

        return $logger;
    }
);

/**
 * Enable logging on the guzzle client of Culturefeed.
 */
$app['culturefeed_http_client_guzzle'] = $app::share(
    $app->extend(
        'culturefeed_http_client_guzzle',
        function (\Guzzle\Http\Client $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('culturefeed');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                MESSAGE_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);

$app['expense_report_api'] = $app::share(
    $app->extend(
        'expense_report_api',
        function (\CultuurNet\UiTPASBeheer\ExpenseReport\ExpenseReportApiService $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('expense_report_api');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                MESSAGE_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);

$app['datavalidation_guzzle_client'] = $app::share(
    $app->extend(
        'datavalidation_guzzle_client',
        function (\Guzzle\Http\Client $service, \Silex\Application $app) {
            /** @var \Psr\Log\LoggerInterface $logger */
            $logger = $app['third_party_api_logger_factory']('datavalidation');

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                MESSAGE_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);
