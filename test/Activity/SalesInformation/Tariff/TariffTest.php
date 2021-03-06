<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Tariff;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\Prices;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class TariffTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use TariffTestDataTrait;

    /**
     * @test
     */
    public function it_returns_the_tariff_data()
    {
        $tariff = $this->getSampleCouponTariff();

        $this->assertEquals(
            TariffType::COUPON(),
            $tariff->getType()
        );
        $this->assertEquals(
            $this->getSamplePrices(),
            $tariff->getPrices()
        );
        $this->assertEquals(
            new StringLiteral('coupon-id-1'),
            $tariff->getId()
        );
        $this->assertFalse($tariff->hasReachedMaximum());

        $tariff = $tariff->withMaximumReached(true);

        $this->assertTrue($tariff->hasReachedMaximum());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_an_id_is_provided_to_a_kansentarief_tariff()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            new Prices(),
            new StringLiteral('coupon-id-1')
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_an_id_is_not_provided_to_a_non_kansentarief_tariff()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new Tariff(
            new StringLiteral('Cultuurwaardebon'),
            TariffType::COUPON(),
            new Prices()
        );
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->getSampleCouponTariff());
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/tariff-coupon.json');
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_kansentarief_ticket_sale_opportunity()
    {
        $cfKansentarief = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfKansentarief->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_DEFAULT;
        $cfKansentarief->priceClasses = $this->getCultureFeedPriceClasses();

        $expected = new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            $this->getSamplePrices()
        );

        $actual = Tariff::fromCultureFeedTicketSaleOpportunity($cfKansentarief);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_coupon_ticket_sale_opportunity()
    {
        $cfTicketSaleCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfTicketSaleCoupon->name = 'Cultuurwaardebon';
        $cfTicketSaleCoupon->id = 'coupon-id-1';

        $cfCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfCoupon->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_COUPON;
        $cfCoupon->priceClasses = $this->getCultureFeedPriceClasses();
        $cfCoupon->ticketSaleCoupon = $cfTicketSaleCoupon;

        $expected = new Tariff(
            new StringLiteral('Cultuurwaardebon'),
            TariffType::COUPON(),
            $this->getSamplePrices(),
            new StringLiteral('coupon-id-1')
        );

        $actual = Tariff::fromCultureFeedTicketSaleOpportunity($cfCoupon);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_sets_maximum_reached_when_a_culturefeed_ticket_sale_opportunity_has_reached_its_maximum()
    {
        $cfKansentarief = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfKansentarief->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_DEFAULT;
        $cfKansentarief->priceClasses = $this->getCultureFeedPriceClasses();
        $cfKansentarief->buyConstraintReason =
            \CultureFeed_Uitpas_Event_TicketSale_Opportunity::BUY_CONSTRAINT_MAXIMUM_REACHED;

        $expected = (new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            $this->getSamplePrices()
        ))->withMaximumReached();

        $actual = Tariff::fromCultureFeedTicketSaleOpportunity($cfKansentarief);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_only_knows_how_to_instantiate_coupons_and_kansentarief()
    {
        $cfTicketSale = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfTicketSale->type = 'FOO';

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Provided $ticketSale argument is of an unknown type "FOO".'
        );
        Tariff::fromCultureFeedTicketSaleOpportunity($cfTicketSale);
    }

    /**
     * @return \CultureFeed_Uitpas_Event_PriceClass[]
     */
    private function getCultureFeedPriceClasses()
    {
        $cfFirstPriceClass = new \CultureFeed_Uitpas_Event_PriceClass();
        $cfFirstPriceClass->name = 'Rang 1';
        $cfFirstPriceClass->price = 30;
        $cfFirstPriceClass->tariff = 22;

        $cfSecondPriceClass = new \CultureFeed_Uitpas_Event_PriceClass();
        $cfSecondPriceClass->name = 'Rang 2';
        $cfSecondPriceClass->price = 15;
        $cfSecondPriceClass->tariff = 11;

        $cfThirdPriceClass = new \CultureFeed_Uitpas_Event_PriceClass();
        $cfThirdPriceClass->name = 'Rang 3+';
        $cfThirdPriceClass->price = 7.5;
        $cfThirdPriceClass->tariff = 5.5;

        return [
            $cfFirstPriceClass,
            $cfSecondPriceClass,
            $cfThirdPriceClass,
        ];
    }
}
