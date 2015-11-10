<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CouponControllerProvider implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $app['coupon_controller'] = $app->share(
            function (Application $app) {
                return new CouponController($app['coupon_service']);
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/passholders/{uitpasNumber}/coupons', 'coupon_controller:getCouponsForPassholder');

        return $controllers;
    }
}
