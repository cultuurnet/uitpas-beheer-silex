<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class ContactInformationJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContactInformationJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new ContactInformationJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_contact_information_json_object()
    {
        $expected = (new ContactInformation())
            ->withEmail(new EmailAddress('zyrani_.hotmail.com@mailinator.com'))
            ->withTelephoneNumber(new StringLiteral('0488694231'))
            ->withMobileNumber(new StringLiteral('0499748596'));

        $json = file_get_contents(__DIR__ . '/../data/properties/contact-information-complete.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));
        //$this->assertTrue($expected->sameValueAs($actual));
        $this->assertEquals($expected->jsonSerialize(), $actual->jsonSerialize());
    }
}
