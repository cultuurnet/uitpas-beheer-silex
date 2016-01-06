<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class CouponService extends CounterAwareUitpasService implements CouponServiceInterface
{
    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);
    }

    /**
     * @inheritDoc
     */
    public function getCouponsForPassholder(UiTPASNumber $uitpasNumber)
    {
        $couponsResultSet = $this->getUitpasService()->getCouponsForPassholder(
            $uitpasNumber->toNative(),
            $this->getCounterConsumerKey()
        );

        $coupons = array_map(
            function ($cfCoupon) {
                return Coupon::fromCultureFeedCoupon($cfCoupon);
            },
            $couponsResultSet->objects
        );

        return $coupons;
    }
}
