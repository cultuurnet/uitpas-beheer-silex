<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
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

    /**
     * @test
     */
    public function it_throws_an_exception_when_date_is_not_set()
    {
        $json = '{"place": "Casablanca"}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "date".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }

    /**
     * @test
     */
    public function it_does_not_accept_time()
    {
        $json = '{"date":"1983-10-14T23:00:00.000Z","place":"Casablanca"}';

        $this->setExpectedException(
            BirthDateInvalidException::class,
            'Invalid birth date "1983-10-14T23:00:00.000Z"'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }
}
