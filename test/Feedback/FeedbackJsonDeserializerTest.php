<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class FeedbackJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    use FeedbackTestDataTrait;

    /**
     * @var FeedbackJsonDeserializer
     */
    private $deserializer;

    public function setUp()
    {
        $this->deserializer = new FeedbackJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_valid_feedback_object()
    {
        $json = file_get_contents(__DIR__ . '/data/feedback.json');

        $expected = $this->getFeedback();

        $actual = $this->deserializer->deserialize(
            new StringLiteral($json)
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_email_is_invalid()
    {
        $json = '{"name": "Alain", "counter": "Het Eiland", "email": "protput.com", "message": "Test"}';

        $this->setExpectedException(
            IncorrectParameterValueException::class,
            'Incorrect value for parameter "email".'
        );

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }

    /**
     * @test
     * @dataProvider missingPropertyDataProvider
     */
    public function it_throws_an_exception_when_a_property_is_missing($json, $property)
    {
        $this->setExpectedException(
            MissingPropertyException::class,
            sprintf(
                'Missing property "%s".',
                $property
            )
        );

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }

    /**
     * @return array
     */
    public function missingPropertyDataProvider()
    {
        return [
            [
                '{}',
                'name',
            ],
            [
                '{"name": ""}',
                'name',
            ],
            [
                '{"name": "Alain"}',
                'counter',
            ],
            [
                '{"name": "Alain", "counter": ""}',
                'counter',
            ],
            [
                '{"name": "Alain", "counter": "Het Eiland"}',
                'email',
            ],
            [
                '{"name": "Alain", "counter": "Het Eiland", "email": ""}',
                'email',
            ],
            [
                '{"name": "Alain", "counter": "Het Eiland", "email": "protput@heteiland.be"}',
                'message',
            ],
            [
                '{"name": "Alain", "counter": "Het Eiland", "email": "protput@heteiland.be", "message": ""}',
                'message',
            ],
        ];
    }
}
