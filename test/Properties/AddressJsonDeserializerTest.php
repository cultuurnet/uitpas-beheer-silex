<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class AddressJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddressJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new AddressJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_address_json_object()
    {
        $expected = (new Address(
            new StringLiteral('1090'),
            new StringLiteral('Jette (Brussel)')
        ))->withStreet(
            new StringLiteral('Rue Ferd 123 /0001')
        );

        $json = file_get_contents(__DIR__ . '/data/address-complete.json');
        $actual = $this->deserializer->deserialize(new StringLiteral($json));

        $this->assertTrue($expected->sameValueAs($actual));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_the_postal_code_is_missing()
    {
        $json = '{"city": "Jette (Brussel)"}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "postalCode".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_the_city_is_missing()
    {
        $json = '{"postalCode": "1090"}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "city".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }
}
