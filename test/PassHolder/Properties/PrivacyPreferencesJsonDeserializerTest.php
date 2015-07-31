<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class PrivacyPreferencesJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrivacyPreferencesJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new PrivacyPreferencesJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_privacy_preferences_object()
    {
        $expected = new PrivacyPreferences(
            PrivacyPreferenceEmail::ALL(),
            PrivacyPreferenceSMS::NOTIFICATION()
        );

        $json = file_get_contents(__DIR__ . '/../data/properties/privacy-preferences-complete.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));
        $this->assertTrue($expected->sameValueAs($actual));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_email_is_not_set()
    {
        $json = '{"sms": true}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "email".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_sms_is_not_set()
    {
        $json = '{"email": true}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "sms".'
        );

        $this->deserializer->deserialize(new StringLiteral($json));
    }
}
