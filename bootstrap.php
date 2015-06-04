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
 * Cross-origin resource sharing.
 */
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), array(
    'cors.allowOrigin' => implode(' ', $app['config']['cors']['origins']),
    'cors.allowCredentials' => true
));

/**
 * Url generator.
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * User session service.
 */
$app->register(new CultuurNet\UiTIDProvider\Session\UserSessionServiceProvider());

/**
 * Authentication service.
 */
$app->register(new CultuurNet\UiTIDProvider\Auth\AuthServiceProvider($app['session']), array(
    'uitid.base_url' => $app['config']['uitid']['base_url'],
    'uitid.consumer.key' => $app['config']['uitid']['consumer']['key'],
    'uitid.consumer.secret' => $app['config']['uitid']['consumer']['secret'],
));

return $app;
