<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\StringLiteral\StringLiteral;

class NameJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NameJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new NameJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_name_json_object()
    {
        $expected = (new Name(
            new StringLiteral('Layla'),
            new StringLiteral('Zyrani')
        ))->withMiddleName(
            new StringLiteral('Zooni')
        );

        $json = file_get_contents(__DIR__ . '/../data/properties/name-complete.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));
        $this->assertTrue($expected->sameValueAs($actual));
    }
}
