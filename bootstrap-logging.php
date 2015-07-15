<?php
/**
 * @file
 */

/** @var \Silex\Application $app */

$app['cultuurnet_search'] = $app->share(
    $app->extend(
        'cultuurnet_search',
        function(\CultuurNet\Search\Guzzle\Service $service) {
            $logger = new Monolog\Logger('cultuurnet_search');
            $logger->pushHandler(
                new \Monolog\Handler\StreamHandler(__DIR__ . '/log/cultuurnet_search.log'),
                \Monolog\Logger::NOTICE
            );

            $logPlugin = new \Guzzle\Plugin\Log\LogPlugin(
                new \Guzzle\Log\PsrLogAdapter($logger),
                \Guzzle\Log\MessageFormatter::DEBUG_FORMAT
            );

            $service->addSubscriber($logPlugin);

            return $service;
        }
    )
);
