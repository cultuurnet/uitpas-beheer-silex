<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SchoolJsonDeserializer
     */
    protected $deserializer;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->deserializer = new SchoolJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_school_json_object()
    {
        $expected = new School(
            new StringLiteral('unique-id'),
            new StringLiteral('Saint Whatever Institute')
        );

        $json = file_get_contents(__DIR__ . '/data/school.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_id_is_not_set()
    {
        $json = '{"name": "University of Life"}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "id".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }
}
