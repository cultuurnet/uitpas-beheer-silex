<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class BirthInformationJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BirthInformationJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new BirthInformationJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_birth_information_json_object()
    {
        $dateTime = new \DateTime('1976-08-13');

        $expected = (new BirthInformation(
            Date::fromNativeDateTime($dateTime)
        ))->withPlace(
            new StringLiteral('Casablanca')
        );

        $json = file_get_contents(__DIR__ . '/../data/properties/birth-information-complete.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));
        $this->assertTrue($expected->sameValueAs($actual));
    }
}
