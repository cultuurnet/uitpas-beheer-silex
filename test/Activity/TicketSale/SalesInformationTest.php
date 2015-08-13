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
    public function it_sets_maximum_reached_to_true_only_if_all_tariffs_have_reached_their_maximum()
    {
        $salesInformation = $this->getSampleSalesInformation()
            ->withTariff(
                $this->getSampleKansentariefTariff()
                    ->withMaximumReached(true)
            )
            ->withTariff(
                $this->getSampleCouponTariff()
                    ->withMaximumReached(true)
            );
        $this->assertTrue($salesInformation->isMaximumReached());

        $salesInformation = $this->getSampleSalesInformation()
            ->withTariff(
                $this->getSampleKansentariefTariff()
                    ->withMaximumReached(false)
            )
            ->withTariff(
                $this->getSampleCouponTariff()
                    ->withMaximumReached(true)
            );
        $this->assertFalse($salesInformation->isMaximumReached());

        $salesInformation = $this->getSampleSalesInformation()
            ->withTariff(
                $this->getSampleKansentariefTariff()
                    ->withMaximumReached(true)
            )
            ->withTariff(
                $this->getSampleCouponTariff()
                    ->withMaximumReached(false)
            );
        $this->assertFalse($salesInformation->isMaximumReached());
    }
}
