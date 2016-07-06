<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;

class CouponController
{
    protected $couponService;

    /**
     * @param CouponServiceInterface $couponService
     */
    public function __construct(CouponServiceInterface $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
    * @param $uitpasNumber
    * @return JsonResponse
    */
    public function getCouponsForPassholder($uitpasNumber)
    {
        $max = 50; // Decision: constant in controller, or given by angular via querystring.
        $coupons = $this->couponService->getCouponsForPassholder(new UiTPASNumber($uitpasNumber));

        $serializedCoupons = array_map(
            function (Coupon $coupon) {
                return $coupon->jsonSerialize();
            },
            $coupons
        );

        return JsonResponse::create($serializedCoupons)
          ->setPrivate();
    }
}
