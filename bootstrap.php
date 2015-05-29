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
 * Url generator.
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Authentication services.
 */
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new CultuurNet\UiTIDProvider\AuthServiceProvider($app['session']), array(
    'uitid.base_url' => $app['config']['uitid']['base_url'],
    'uitid.consumer.key' => $app['config']['uitid']['consumer']['key'],
    'uitid.consumer.secret' => $app['config']['uitid']['consumer']['secret'],
));

return $app;
