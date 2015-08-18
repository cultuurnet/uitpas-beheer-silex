<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;

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
        $this->assertFalse($tariff->hasReachedMaximum());

        $tariff = $tariff->withMaximumReached(true);

        $this->assertTrue($tariff->hasReachedMaximum());
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->getSampleCouponTariff());
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/tariff-coupon.json');
    }
}
