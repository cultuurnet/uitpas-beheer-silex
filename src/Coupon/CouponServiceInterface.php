<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

interface CouponServiceInterface
{
    /**
     * Get a list of all the available coupons for the passholder with the given
     * UiTPAS-number.
     *
     * @param UiTPASNumber $uitpasNumber
     * @return Coupon[]
     */
    public function getCouponsForPassholder(UiTPASNumber $uitpasNumber);
}
