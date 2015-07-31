<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\AddressJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\NameJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferencesJsonDeserializer;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var PassHolderJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new PassHolderJsonDeserializer(
            new NameJsonDeserializer(),
            new AddressJsonDeserializer(),
            new BirthInformationJsonDeserializer(),
            new ContactInformationJsonDeserializer(),
            new PrivacyPreferencesJsonDeserializer()
        );
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_passholder_json_object()
    {
        $expected = $this->getCompletePassHolderUpdate();

        $json = file_get_contents(__DIR__ . '/data/passholder-update.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));
        $this->assertEquals($expected->jsonSerialize(), $actual->jsonSerialize());
    }
}
