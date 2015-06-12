<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
 * Authentication controllers.
 */
$authController = new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider(
    $app['uitid_auth_service'],
    $app['session'],
    $app['url_generator']
);
$app->mount('culturefeed/oauth', $authController);

/**
 * Authentication verification callback.
 */
$checkAuthentication = function (Request $request, Application $app) {
    /* @var CultuurNet\UiTIDProvider\Session\UserSession $session */
    $session = $app['session'];

    if (is_null($session->getUser())) {
        return new Response('Access denied.', 403);
    } else {
        return null;
    }
};

/**
 * API callbacks for UiTID user data and methods.
 *
 * @var \Silex\ControllerCollection $userControllers
 */
$userControllers = $app['controllers_factory'];
$userControllers->before($checkAuthentication);
$app->mount('uitid', new \CultuurNet\UiTIDProvider\UserControllerProvider($userControllers));

/**
 * API callbacks for Counters.
 */
$app->mount('counters', new \CultuurNet\UiTPASBeheer\Counter\CounterControllerProvider());

$app->run();
