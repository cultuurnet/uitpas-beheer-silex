<?php

use CultuurNet\UiTPASBeheer\Auth\AuthControllerProvider;
use CultuurNet\UiTPASBeheer\GroupPass\GroupPassControllerProvider;
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
$app['security.firewalls'] = [
    'public' => [
        'pattern' => '^/group-pass',
    ],
    'authentication' => [
        'pattern' => '^/culturefeed/oauth',
    ],
    'cors-preflight' => [
        'pattern' => $app['cors_preflight_request_matcher'],
    ],
    'secured' => [
        'pattern' => '^.*$',
        'uitid' => [
            'roles' => isset($app['config']['roles']) ? $app['config']['roles'] : [],
        ],
        'users' => $app['uitid_firewall_user_provider'],
    ],
];

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
if ($app['external_auth_enabled']) {
    $app->mount('culturefeed/oauth', new AuthControllerProvider());
} else {
    $app->mount('culturefeed/oauth', new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider());
}

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
 * API callbacks for CheckIn Codes.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\CheckInCode\CheckInCodeControllerProvider());

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
$app->mount('/', new \CultuurNet\UiTPASBeheer\Membership\Association\AssociationControllerProvider());

/**
 * API callbacks for Coupons.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Coupon\CouponControllerProvider());

/**
 * API callbacks for points history.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\PointsHistory\PointsHistoryControllerProvider());

/**
 * API callbacks for Help.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Help\HelpControllerProvider());

/**
 * API callbacks for Feedback.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\Feedback\FeedbackControllerProvider());

/**
 * API callbacks for Card Systems.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\CardSystem\CardSystemControllerProvider());

$app->mount('/', new \CultuurNet\UiTPASBeheer\School\SchoolControllerProvider());

/**
 * API callbacks for Balie insights.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\BalieInsights\BalieInsightsControllerProvider());

/**
 * API callbacks for data validation.
 */
$app->mount('/', new \CultuurNet\UiTPASBeheer\DataValidation\DataValidationControllerProvider());

/**
 * API callbacks for group passes
 */
$app->mount('group-pass', new GroupPassControllerProvider());

$app->run();
