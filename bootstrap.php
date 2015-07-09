<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

/**
 * Load the config.
 */
$app->register(new \DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/config.yml'));

/**
 * Turn debug on or off.
 */
$app['debug'] = $app['config']['debug'] === true;

/**
 * Configure CORS.
 */
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), array(
    'cors.allowOrigin' => implode(' ', $app['config']['cors']['origins']),
    'cors.allowCredentials' => true
));

/**
 * Exception handling.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Exception\ExceptionHandlerServiceProvider());

/**
 * Session service.
 */
$app->register(new \Silex\Provider\SessionServiceProvider());

/**
 * Security services.
 */
$app->register(new \Silex\Provider\SecurityServiceProvider());
$app->register(new \CultuurNet\UiTIDProvider\Security\UiTIDSecurityServiceProvider());

/**
 * CultureFeed services.
 */
$app->register(new \CultuurNet\UiTIDProvider\CultureFeed\CultureFeedServiceProvider(), array(
    'culturefeed.endpoint' => $app['config']['uitid']['base_url'],
    'culturefeed.consumer.key' => $app['config']['uitid']['consumer']['key'],
    'culturefeed.consumer.secret' => $app['config']['uitid']['consumer']['secret'],
));

/**
 * Url generator.
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * UiTID Authentication services.
 */
$app->register(new CultuurNet\UiTIDProvider\Auth\AuthServiceProvider());

/**
 * UiTID User services.
 */
$app->register(new CultuurNet\UiTIDProvider\User\UserServiceProvider());

/**
 * UiTPAS service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\UiTPAS\UiTPASServiceProvider());

/**
 * UiTPAS Counter service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Counter\CounterServiceProvider());

/**
 * UiTPAS PassHolder service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\PassHolder\PassHolderServiceProvider());

/**
 * UiTPAS Advantage service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Advantage\AdvantageServiceProvider());

/**
 * Clock service.
 */
$app->register(
    new \CultuurNet\UiTPASBeheer\ClockServiceProvider(),
    ['clock.timezone' => 'Europe/Brussels']
);

return $app;
