<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Registration;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    use RegistrationTestDataTrait;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new RegistrationJsonDeserializer(
            new KansenStatuutJsonDeserializer()
        );
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_passholder_uid_is_missing()
    {
        $json = '{"reason": "LOSS_THEFT"}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "passholderUid".'
        );

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_reason_is_missing()
    {
        $json = '{"passholderUid": "5"}';

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "reason".'
        );

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }

    /**
     * @test
     *
     * @param string $jsonFileUri
     * @param Registration $expected
     *
     * @dataProvider registrationJsonDataProvider
     */
    public function it_ignores_optional_properties_that_are_not_present(
        $jsonFileUri,
        Registration $expected
    ) {
        $json = file_get_contents($jsonFileUri);

        $actual = $this->deserializer->deserialize(
            new StringLiteral($json)
        );

        $this->assertEquals($expected, $actual);
    }

    public function registrationJsonDataProvider()
    {
        return [
            [
                __DIR__ . '/../data/registration-minimal.json',
                $this->getMinimalRegistration()
            ],
            [
                __DIR__ . '/../data/registration-complete.json',
                $this->getCompleteRegistration()
            ]
        ];
    }
}
