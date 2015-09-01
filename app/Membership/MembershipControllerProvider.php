<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Membership;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class MembershipControllerProvider implements ControllerProviderInterface
{
    /**
     * @inheritdoc
     */
    public function connect(Application $app)
    {
        $app['membership_controller'] = $app->share(
            function (Application $app) {
                return new MembershipController(
                    $app['uitpas'],
                    $app['counter_consumer_key']
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get(
            '/passholders/{uitpasNumber}/profile',
            'membership_controller:listing'
        );

        $controllers->post(
            '/passholders/{uitpasNumber}/profile/memberships',
            'membership_controller:register'
        );

        $controllers->delete(
            '/passholders/{uitpasNumber}/profile/memberships/{associationId}',
            'membership_controller:stop'
        );

        return $controllers;
    }
}
