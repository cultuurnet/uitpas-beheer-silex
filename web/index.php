<?php

use Silex\Application;

/* @var Application $app */
$app = require_once __DIR__ . '/../bootstrap.php';

/**
 * Enable CORS.
 */
$app->after($app['cors']);

/**
 * Firewall.
 */
$app['security.firewalls'] = array(
    'authentication' => array(
        'pattern' => '^/culturefeed/oauth',
    ),
    'secured' => array(
        'pattern' => '^.*$',
        'uitid' => true,
        'users' => $app['uitid_firewall_user_provider'],
    ),
);

/**
 * Register controllers as services.
 */
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

/**
 * API callbacks for authentication.
 */
$app->mount('culturefeed/oauth', new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider());

/**
 * API callbacks for UiTID user data and methods.
 */
$app->mount('uitid', new \CultuurNet\UiTIDProvider\User\UserControllerProvider());

/**
 * API callbacks for Counters.
 */
$app->mount('counter', new \CultuurNet\UiTPASBeheer\Counter\CounterControllerProvider());

/**
 * API callbacks for PassHolders.
 */
$app->mount('passholder', new \CultuurNet\UiTPASBeheer\PassHolder\PassHolderControllerProvider());

$app->run();
