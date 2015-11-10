<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use ValueObjects\StringLiteral\StringLiteral;

class CouponTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_include_the_id_and_name_from_a_culturefeed_coupon()
    {
        $cfCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfCoupon->id = '182';
        $cfCoupon->name = 'SUPER DUPER DISCOUNT COUPON!';

        $couponFromCfCoupon = Coupon::fromCultureFeedCoupon($cfCoupon);

        $expectedCoupon = new Coupon(
            new StringLiteral('182'),
            new StringLiteral('SUPER DUPER DISCOUNT COUPON!')
        );

        $this->assertEquals($expectedCoupon, $couponFromCfCoupon);
    }

    /**
     * @test
     */
    public function it_can_be_serialized_with_all_of_its_properties()
    {
        $coupon = new Coupon(
            new StringLiteral('182'),
            new StringLiteral('SUPER DUPER DISCOUNT COUPON!')
        );

        $serializedCoupon = $coupon->jsonSerialize();
        $expectedCoupon = [
            'id' => '182',
            'name' => 'SUPER DUPER DISCOUNT COUPON!',
        ];

        $this->assertEquals($expectedCoupon, $serializedCoupon);
    }
}
