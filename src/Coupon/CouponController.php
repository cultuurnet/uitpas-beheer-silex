<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
    * @return Response
    */
    public function getCouponsForPassholder($uitpasNumber)
    {
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
