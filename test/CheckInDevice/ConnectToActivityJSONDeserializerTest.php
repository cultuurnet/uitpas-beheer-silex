<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;


use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class ConnectToActivityJSONDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConnectToActivityJSONDeserializer
     */
    private $deserializer;

    public function setUp()
    {
        $this->deserializer = new ConnectToActivityJSONDeserializer();
    }

    /**
     * @test
     */
    public function it_deserializes_from_JSON()
    {
        $json = new StringLiteral('{"activityId": "123-456"}');
        $activityId = $this->deserializer->deserialize($json);

        $this->assertEquals(
            new StringLiteral('123-456'),
            $activityId
        );
    }

    /**
     * @test
     */
    public function it_fails_when_property_activityId_is_missing()
    {
        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "activityId".'
        );

        $json = new StringLiteral('{}');
        $this->deserializer->deserialize($json);
    }
}
