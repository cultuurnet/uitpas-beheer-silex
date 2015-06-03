<?php

$app = require_once __DIR__ . '/../bootstrap.php';

/**
 * Authentication controllers.
 */
$app->mount('culturefeed/oauth', new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider(
    $app['uitid_auth_service'],
    $app['session'],
    $app['url_generator']
));

$app->run();
