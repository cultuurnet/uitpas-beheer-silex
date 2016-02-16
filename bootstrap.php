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
 * Configure CORS.
 */
$app->register(
    new JDesrosiers\Silex\Provider\CorsServiceProvider(),
    array(
        'cors.allowOrigin' => implode(' ', $app['config']['cors']['origins']),
        'cors.allowCredentials' => true,
    )
);

/**
 * Exception handling.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Exception\ExceptionHandlerServiceProvider());

/**
 * Session service.
 */
$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new CultuurNet\UiTIDProvider\Session\SessionConfigurationProvider());

/**
 * Security services.
 */
$app->register(new \Silex\Provider\SecurityServiceProvider());
$app->register(new \CultuurNet\UiTIDProvider\Security\UiTIDSecurityServiceProvider());

/**
 * Override the default authentication provider for uitid with our own
 * that adds additional roles to specific users.
 */
$app['security.authentication_provider.uitid._proto'] = $app->protect(
    function ($name, $options) use ($app) {
        return $app->share(
            function () use ($app, $options) {
                $authenticator = new \CultuurNet\UiTIDProvider\Security\UiTIDAuthenticator($app['uitid_user_service']);
                $roles = isset($options['roles']) ? $options['roles'] : [];
                if (empty($roles)) {
                    return $authenticator;
                }

                $authenticator = new \CultuurNet\UiTPASBeheer\Security\RoleAddingAuthenticationProviderDecorator(
                    $authenticator,
                    $roles
                );

                return $authenticator;
            }
        );
    }
);

/**
 * CultureFeed services.
 */
$app->register(
    new \CultuurNet\UiTIDProvider\CultureFeed\CultureFeedServiceProvider(),
    array(
        'culturefeed.endpoint' => $app['config']['uitid']['base_url'],
        'culturefeed.consumer.key' => $app['config']['uitid']['consumer']['key'],
        'culturefeed.consumer.secret' => $app['config']['uitid']['consumer']['secret'],
    )
);

/**
 * Url generator.
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * UiTID Authentication services.
 */
$app->register(new CultuurNet\UiTIDProvider\Auth\AuthServiceProvider());

/**
 * UiTID User services.
 */
$app->register(new CultuurNet\UiTIDProvider\User\UserServiceProvider());

/**
 * CultuurNet services.
 */
$app->register(
    new \CultuurNet\UiTPASBeheer\CultuurNetServiceProvider(),
    array(
        'cultuurnet.search.endpoint' => $app['config']['search']['base_url'],
    )
);

/**
 * UiTPAS service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\UiTPAS\UiTPASServiceProvider());

/**
 * UiTPAS Counter service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Counter\CounterServiceProvider());

/**
 * UiTPAS Identity service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Identity\IdentityServiceProvider());

/**
 * UiTPAS PassHolder service.
 */
$app->register(
    new \CultuurNet\UiTPASBeheer\PassHolder\PassHolderServiceProvider(),
    array(
        'passholder.export.limit_per_api_request' => $app['config']['export']['limit_per_api_request'],
    )
);

/**
 * UiTPAS Activity service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Activity\ActivityServiceProvider());

/**
 * UiTPAS Advantage service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Advantage\AdvantageServiceProvider());

/**
 * UiTPAS Membership service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Membership\MembershipServiceProvider());

/**
 * UiTPAS Legacy service(s).
 */
$app->register(new \CultuurNet\UiTPASBeheer\Legacy\LegacyServiceProvider());

/**
 * UiTPAS KansenStatuut service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutServiceProvider());

/**
 * UiTPAS CheckIn Device service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\CheckInDevice\CheckInDeviceServiceProvider());

/**
 * UiTPAS ExpenseReport services.
 */
$app->register(new \CultuurNet\UiTPASBeheer\ExpenseReport\ExpenseReportServiceProvider());

/**
 * UiTPAS User service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\User\UserServiceProvider());

/**
 * UiTPAS Member service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Counter\Member\MemberServiceProvider());

/**
 * UiTPAS Association service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Membership\Association\AssociationServiceProvider());

/**
 * UiTPAS Coupon service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\Coupon\CouponServiceProvider());

/**
 * UiTPAS Feedback service.
 */
$app->register(
    new \CultuurNet\UiTPASBeheer\Feedback\FeedbackServiceProvider(),
    [
        'feedback.from' => $app['config']['feedback']['from'],
        'feedback.to' => $app['config']['feedback']['to'],
        'feedback.subject' => $app['config']['feedback']['subject'],
    ]
);

/**
 * UiTPAS Points History service.
 */
$app->register(new \CultuurNet\UiTPASBeheer\PointsHistory\PointsHistoryServiceProvider());

$app->register(new \CultuurNet\UiTPASBeheer\CardSystem\CardSystemServiceProvider());

/**
 * Clock service.
 */
$app->register(
    new \CultuurNet\UiTPASBeheer\ClockServiceProvider(),
    ['clock.timezone' => 'Europe/Brussels']
);

/**
 * Mailing service.
 */
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.use_spool'] = false;
if (isset($app['config']['swiftmailer.options'])) {
    $app['swiftmailer.options'] = $app['config']['swiftmailer.options'];
}

$app->register(new \CultuurNet\UiTPASBeheer\School\SchoolServiceProvider());

/**
 * Load additional bootstrap files.
 */
foreach ($app['config']['bootstrap'] as $identifier => $enabled) {
    if (true === $enabled) {
        require __DIR__ . "/bootstrap/{$identifier}.php";
    }
}

return $app;
