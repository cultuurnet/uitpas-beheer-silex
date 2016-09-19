<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @param string $uitpasNumber
     * @return JsonResponse
     */
    public function getCouponsForPassholder(Request $request, $uitpasNumber)
    {
        $coupons = $this->couponService->getCouponsForPassholder(new UiTPASNumber($uitpasNumber), $request->query->get('max'), $request->query->get('start'));

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
