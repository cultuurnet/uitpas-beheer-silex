<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/* @var Application $app */
$app = require_once __DIR__ . '/../bootstrap.php';

/**
 * Enable CORS.
 */
$app->after($app['cors']);

$app->before(
    function (Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            if (null === $data) {
                // Decoding failed. Probably the submitted JSON is not correct.
                return Response::create('Unable to decode the submitted body. Is it valid JSON?', 400);
            }
            $request->request->replace(is_array($data) ? $data : array());
        }
    }
);

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
 * Enable application/json post data.
 */
$app->register(new \CultuurNet\UiTPASBeheer\JsonPostDataServiceProvider());

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

$app->get(
    'swagger.json',
    function (Request $request) {
        $file = new SplFileInfo(__DIR__ . '/swagger.json');
        return new \Symfony\Component\HttpFoundation\BinaryFileResponse(
            $file,
            200,
            [
                'Content-Type' => 'application/json',
            ]
        );
    }
);

$app->run();
