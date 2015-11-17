<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\Properties\PeriodConstraint;
use CultuurNet\UiTPASBeheer\Properties\PeriodType;
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
        $remainingTotal = new PeriodConstraint(
            PeriodType::fromNative('WEEK'),
            new Integer(5)
        );

        $coupon = (new Coupon(
            new StringLiteral('182'),
            new StringLiteral('SUPER DUPER DISCOUNT COUPON!')
        ))
            ->withRemainingTotal($remainingTotal)
            ->withDescription(new StringLiteral('SUPER DUPER DISCOUNT COUPON! Much WoW'))
            ->withStartDate(new Integer(1443654000))
            ->withExpirationDate(new Integer(1475276400));

        $serializedCoupon = $coupon->jsonSerialize();
        $expectedCoupon = [
            'id' => '182',
            'name' => 'SUPER DUPER DISCOUNT COUPON!',
            'description' => 'SUPER DUPER DISCOUNT COUPON! Much WoW',
            'expirationDate' => '2016-09-30',
            'startDate' => '2015-09-30',
            'remainingTotal' => [
                'period' => 'WEEK',
                'volume' => 5,
            ],
        ];

        $this->assertEquals($expectedCoupon, $serializedCoupon);
    }

    /**
     * @test
     */
    public function it_can_include_extra_properties()
    {
        $cfRemainingTotal = new \CultureFeed_Uitpas_PeriodConstraint();
        $cfRemainingTotal->type = 'WEEK';
        $cfRemainingTotal->volume = 5;

        $cfCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfCoupon->id = '182';
        $cfCoupon->name = 'SUPER DUPER DISCOUNT COUPON!';
        $cfCoupon->description = 'SUPER DUPER DISCOUNT COUPON! Much WoW';
        $cfCoupon->remainingTotal = $cfRemainingTotal;
        $cfCoupon->validTo = 1475276400;
        $cfCoupon->validFrom = 1443654000;

        $couponFromCfCoupon = Coupon::fromCultureFeedCoupon($cfCoupon);

        $expectedRemainingTotal = new PeriodConstraint(
            PeriodType::fromNative('WEEK'),
            new Integer(5)
        );

        $expectedCoupon = (new Coupon(
            new StringLiteral('182'),
            new StringLiteral('SUPER DUPER DISCOUNT COUPON!')
        ))
            ->withRemainingTotal($expectedRemainingTotal)
            ->withDescription(new StringLiteral('SUPER DUPER DISCOUNT COUPON! Much WoW'))
            ->withStartDate(new Integer(1443654000))
            ->withExpirationDate(new Integer(1475276400));

        $this->assertEquals($expectedCoupon, $couponFromCfCoupon);
    }
}
