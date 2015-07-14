<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class AdvantageIdentifierJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var AdvantageIdentifierJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new AdvantageIdentifierJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_an_advantage_identifier()
    {
        $json = $this->getJson('Advantage/data/welcomeAdvantageIdentifier.json');
        $identifier = $this->deserializer->deserialize(
            new StringLiteral($json)
        );

        $expectedType = AdvantageType::WELCOME();
        $expectedId = new StringLiteral('10');

        $this->assertEquals($expectedType, $identifier->getType());
        $this->assertEquals($expectedId, $identifier->getId());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_the_id_property_is_missing()
    {
        $json = $this->getJson('Advantage/data/invalidAdvantageIdentifier.json');

        $this->setExpectedException(MissingPropertyException::class);
        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }
}
