<?php

use Silex\Application;

/* @var Application $app */
$app = require_once __DIR__ . '/../bootstrap.php';

/**
 * Enable CORS.
 */
$app->after($app['cors']);

/**
 * Register controllers as services.
 */
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

/**
 * API callbacks for authentication.
 */
$authController = new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider(
    $app['uitid_auth_service'],
    $app['uitid_user_session_service'],
    $app['url_generator']
);
$app->mount('culturefeed/oauth', $authController);

/**
 * API callbacks for UiTID user data and methods.
 */
$app->mount('uitid', new \CultuurNet\UiTIDProvider\User\UserControllerProvider());

/**
 * API callbacks for Counters.
 */
$app->mount('counters', new \CultuurNet\UiTPASBeheer\Counter\CounterControllerProvider());

$app->run();
