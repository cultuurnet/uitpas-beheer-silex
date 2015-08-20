<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_the_registration_data()
    {
        $id = new StringLiteral('d3a36630-6e1a-4bb6-8f53-18bec43a70b4');
        $priceClass = new PriceClass('Basisprijs');
        $tariffId = new StringLiteral('coupon-id-1');

        $registration = new Registration(
            $id,
            $priceClass
        );

        $this->assertEquals($id, $registration->getActivityId());
        $this->assertEquals($priceClass, $registration->getPriceClass());
        $this->assertNull($registration->getTariffId());

        $registration = $registration->withTariffId($tariffId);

        $this->assertEquals($tariffId, $registration->getTariffId());
    }
}
