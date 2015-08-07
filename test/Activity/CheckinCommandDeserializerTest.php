<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinCommandDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckinCommandDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new CheckinCommandDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_checkin_command()
    {
        $expected = new CheckinCommand(new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'));

        $json = '{"eventCdbid": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee"}';
        $actual = $this->deserializer->deserialize(new StringLiteral($json));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_the_event_id_is_missing()
    {
        $json = '{}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "eventCdbid".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }
}
