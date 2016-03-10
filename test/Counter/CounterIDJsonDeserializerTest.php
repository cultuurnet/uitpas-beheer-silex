<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class CounterIDJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterIDJsonDeserializer
     */
    private $deserializer;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->deserializer = new CounterIDJsonDeserializer();
    }

    /**
     * Provides some valid counter id JSON samples.
     */
    public function validJsonSamples()
    {
        return [
            ['{"id": 10}'],
            ['{"id": "10"}'],
        ];
    }

    /**
     * @test
     * @dataProvider validJsonSamples
     * @param string $validCounterIDJson
     */
    public function deserializes_a_valid_id($validCounterIDJson)
    {
        $id = $this->deserializer->deserialize(
            new StringLiteral(
                $validCounterIDJson
            )
        );

        $this->assertSame('10', $id);
    }

    /**
     * @test
     */
    public function fails_when_id_is_missing()
    {
        $this->setExpectedException(MissingPropertyException::class);

        $this->deserializer->deserialize(
            new StringLiteral('{}')
        );
    }

    /**
     * @test
     */
    public function refuses_an_id_that_is_neither_a_string_nor_numeric()
    {
        $this->setExpectedException(IncorrectParameterValueException::class);

        $this->deserializer->deserialize(
            new StringLiteral('{"id": {"foo": "bar"}}')
        );
    }
}
