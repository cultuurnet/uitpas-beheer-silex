<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class SalesInformationTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use SalesInformationTestDataTrait;

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_tariff_is_provided_with_an_unknown_price_class()
    {
        $salesInformation = $this->getSampleSalesInformation();

        $tariff = new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            (new Prices())->withPricing(
                new PriceClass('Rang 0'),
                new Real(10)
            )
        );

        $this->setExpectedException(\InvalidArgumentException::class);
        $salesInformation->withTariff($tariff);
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->getSampleSalesInformationWithTariffs());
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/sales-information.json');
    }

    /**
     * @test
     */
    public function it_omits_lowest_available_from_json_if_all_tariffs_have_reached_maximum_sales()
    {
        $json = json_encode($this->getSampleSalesInformationWithMaximumReached());
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/sales-information-maximum-reached.json');
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_uitpas_event()
    {
        $cfEvent = new \CultureFeed_Uitpas_Event_CultureEvent();

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

        $cfKansentarief = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfKansentarief->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_DEFAULT;
        $cfKansentarief->priceClasses = array(
            $cfFirstPriceClass,
            $cfSecondPriceClass,
            $cfThirdPriceClass,
        );

        $cfTicketSaleCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfTicketSaleCoupon->name = 'Cultuurwaardebon';

        $cfCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfCoupon->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_COUPON;
        $cfCoupon->priceClasses = array(
            $cfFirstPriceClass,
            $cfSecondPriceClass,
            $cfThirdPriceClass,
        );
        $cfCoupon->ticketSaleCoupon = $cfTicketSaleCoupon;

        $cfMaximumReachedCoupon = clone $cfCoupon;
        $cfMaximumReachedCoupon->buyConstraintReason =
            \CultureFeed_Uitpas_Event_TicketSale_Opportunity::BUY_CONSTRAINT_MAXIMUM_REACHED;

        $cfEvent->ticketSales = array(
            $cfKansentarief,
            $cfCoupon,
            $cfMaximumReachedCoupon,
        );

        $expected = (new SalesInformation(
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(30)
                )
                ->withPricing(
                    new PriceClass('Rang 2'),
                    new Real(15)
                )
                ->withPricing(
                    new PriceClass('Rang 3+'),
                    new Real(7.5)
                )
        ))
            ->withTariff(
                new Tariff(
                    new StringLiteral('Kansentarief'),
                    TariffType::KANSENTARIEF(),
                    $this->getSamplePrices()
                )
            )
            ->withTariff(
                new Tariff(
                    new StringLiteral('Cultuurwaardebon'),
                    TariffType::COUPON(),
                    $this->getSamplePrices()
                )
            )
            ->withTariff(
                new Tariff(
                    new StringLiteral('Cultuurwaardebon'),
                    TariffType::COUPON(),
                    $this->getSamplePrices(),
                    true
                )
            );

        $actual = SalesInformation::fromCultureFeedUitpasEvent($cfEvent);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_falls_back_to_the_old_price_property_when_instantiating_from_a_culturefeed_uitpas_event()
    {
        $cfEvent = new \CultureFeed_Uitpas_Event_CultureEvent();
        $cfEvent->price = 30;

        $expected = new SalesInformation(
            (new Prices())
                ->withPricing(
                    new PriceClass('Standaard'),
                    new Real(30)
                )
        );

        $actual = SalesInformation::fromCultureFeedUitpasEvent($cfEvent);

        $this->assertEquals($expected, $actual);
    }
}
