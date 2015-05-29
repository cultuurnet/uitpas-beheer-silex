<?php

$app = require_once __DIR__ . '/../bootstrap.php';

/**
 * Authentication controllers.
 */
$app->mount('culturefeed/oauth', new \CultuurNet\UiTIDProvider\AuthControllerProvider(
    $app['auth_service'],
    $app['session'],
    $app['url_generator']
));



$app->run();
