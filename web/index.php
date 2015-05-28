<?php

$app = require_once __DIR__.'/../bootstrap.php';

$app->get('/hello', function () {
    return 'Hello!';
});

$app->run();
