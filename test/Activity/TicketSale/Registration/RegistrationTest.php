<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use ValueObjects\Number\Natural;
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
        $amount = new Natural(3);

        $registration = new Registration(
            $id,
            $priceClass
        );

        $this->assertEquals($id, $registration->getActivityId());
        $this->assertEquals($priceClass, $registration->getPriceClass());
        $this->assertNull($registration->getTariffId());

        $registration = $registration->withTariffId($tariffId);

        $this->assertEquals($tariffId, $registration->getTariffId());

        $registration = $registration->withAmount($amount);

        $this->assertEquals($amount, $registration->getAmount());
    }
}
