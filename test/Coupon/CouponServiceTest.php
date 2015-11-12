<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class CouponServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var CouponService
     */
    protected $couponService;

    /**
    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');
        $this->uitpasNumber = new UiTPASNumber('0930000125607');
        $this->couponService = new CouponService($this->uitpas, $this->counterConsumerKey);
    }

    /**
     * @test
     */
    public function it_returns_the_coupons_for_passholder()
    {
        $cfCoupons = array();
        $expected = array();

        for ($i = 1; $i <= 3; $i++) {
            $coupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
            $coupon->id = (string) $i;
            $coupon->name = 'Coupon ' . $i;
            $cfCoupons[] = $coupon;
            $expected[] = Coupon::fromCultureFeedCoupon($coupon);
        }

        $resultSet = new \CultureFeed_ResultSet(
            count($cfCoupons),
            array_values($cfCoupons)
        );

        $this->uitpas->expects($this->any())
            ->method('getCouponsForPassholder')
            ->with($this->uitpasNumber, $this->counterConsumerKey)
            ->willReturn($resultSet);
        $actual = $this->couponService->getCouponsForPassholder($this->uitpasNumber);
        $this->assertEquals($expected, $actual);
    }
}
