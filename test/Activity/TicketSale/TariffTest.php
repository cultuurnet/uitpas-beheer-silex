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
    public function it_returns_the_maximum_reached_status()
    {
        $tariff = $this->getSampleCouponTariff();
        $this->assertFalse($tariff->isMaximumReached());

        $tariff = $tariff->withMaximumReached(true);
        $this->assertTrue($tariff->isMaximumReached());
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
