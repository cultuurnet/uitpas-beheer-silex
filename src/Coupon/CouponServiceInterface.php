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
     * @param integer $max
     * @param integer $start
     * @return Coupon[]
     */
    public function getCouponsForPassholder(UiTPASNumber $uitpasNumber, $max = NULL, $start = NULL);
}
