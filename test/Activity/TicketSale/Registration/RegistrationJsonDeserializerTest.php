<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\Number\Natural;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegistrationJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new RegistrationJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_registration_json_string()
    {
        $json = file_get_contents(__DIR__ . '/../../data/ticket-sale/registration.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));

        $expected = new Registration(
            new StringLiteral('100'),
            new PriceClass('Basisprijs'),
            new TariffId('141327894321897')
        );

        $expected = $expected->withAmount(new Natural(3));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_activity_id_is_missing()
    {
        $json = '{"priceClass": "Basisprijs"}';
        $this->setExpectedException(MissingPropertyException::class);
        $this->deserializer->deserialize(new StringLiteral($json));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_price_class_is_missing()
    {
        $json = '{"activityId": "d3a36630-6e1a-4bb6-8f53-18bec43a70b4"}';
        $this->setExpectedException(MissingPropertyException::class);
        $this->deserializer->deserialize(new StringLiteral($json));
    }
}
