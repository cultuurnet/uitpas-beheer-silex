<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use ValueObjects\Number\Integer;
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
        $coupon = $coupon->withRemainingTotal(new Integer(5));
        $coupon = $coupon->withDescription(new StringLiteral('SUPER DUPER DISCOUNT COUPON! Much WoW'));
        $coupon = $coupon->withStartDate(new Integer(1443654000));
        $coupon = $coupon->withExpirationDate(new Integer(1475276400));

        $serializedCoupon = $coupon->jsonSerialize();
        $expectedCoupon = [
            'id' => '182',
            'name' => 'SUPER DUPER DISCOUNT COUPON!',
            'description' => 'SUPER DUPER DISCOUNT COUPON! Much WoW',
            'expirationDate' => '2016-09-30',
            'startDate' => '2015-09-30',
            'remainingTotal' => 5,
        ];

        $this->assertEquals($expectedCoupon, $serializedCoupon);
    }

    /**
     * @test
     */
    public function it_can_include_extra_properties()
    {
        $remainingTotal = new \CultureFeed_Uitpas_PeriodConstraint();
        $remainingTotal->volume = 5;

        $cfCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfCoupon->id = '182';
        $cfCoupon->name = 'SUPER DUPER DISCOUNT COUPON!';
        $cfCoupon->description = 'SUPER DUPER DISCOUNT COUPON! Much WoW';
        $cfCoupon->remainingTotal = $remainingTotal;
        $cfCoupon->validTo = 1475276400;
        $cfCoupon->validFrom = 1443654000;


        $couponFromCfCoupon = Coupon::fromCultureFeedCoupon($cfCoupon);

        $expectedCoupon = new Coupon(
            new StringLiteral('182'),
            new StringLiteral('SUPER DUPER DISCOUNT COUPON!')
        );
        $expectedCoupon = $expectedCoupon->withRemainingTotal(new Integer(5));
        $expectedCoupon = $expectedCoupon->withDescription(new StringLiteral('SUPER DUPER DISCOUNT COUPON! Much WoW'));
        $expectedCoupon = $expectedCoupon->withStartDate(new Integer(1443654000));
        $expectedCoupon = $expectedCoupon->withExpirationDate(new Integer(1475276400));

        $this->assertEquals($expectedCoupon, $couponFromCfCoupon);
    }
}
