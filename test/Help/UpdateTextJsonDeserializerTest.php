<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class UpdateTextJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateTextJsonDeserializer
     */
    private $deserializer;

    public function setUp()
    {
        $this->deserializer = new UpdateTextJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_fails_when_text_property_is_missing()
    {
        $this->setExpectedException(MissingPropertyException::class);

        $this->deserializer->deserialize(new StringLiteral('{}'));
    }
}
