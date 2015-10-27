<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
    'cors-preflight' => array(
        'pattern' => $app['cors_preflight_request_matcher'],
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
$app->mount('/', new \CultuurNet\UiTPASBeheer\Counter\CounterControllerProvider());

/**
 * API callbacks for identities.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Identity\IdentityControllerProvider());

/**
 * API callbacks for UiTPASes.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\UiTPAS\UiTPASControllerProvider());

/**
 * API callbacks for PassHolders.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\PassHolder\PassHolderControllerProvider());

/**
 * API callbacks for Activities.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Activity\ActivityControllerProvider());

/**
 * API callbacks for Advantages.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Advantage\AdvantageControllerProvider());

/**
 * API callbacks for Memberships.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Membership\MembershipControllerProvider());

/**
 * API callbacks for KansenStatuten.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutControllerProvider());

/**
 * API callbacks for CheckIn Devices.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\CheckInDevice\CheckInDeviceControllerProvider());

/**
 * API callbacks for ExpenseReports.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\ExpenseReport\ExpenseReportControllerProvider());

/**
 * API callbacks for Members.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Counter\Member\MemberControllerProvider());

/**
 * API callbacks for Associations.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Counter\Association\AssociationControllerProvider());

$app->run();
