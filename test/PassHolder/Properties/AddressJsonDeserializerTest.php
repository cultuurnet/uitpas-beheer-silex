<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

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

        $json = file_get_contents(__DIR__ . '/../data/properties/address-complete.json');
        $actual = $this->deserializer->deserialize(new StringLiteral($json));

        $this->assertTrue($expected->sameValueAs($actual));
    }
}
