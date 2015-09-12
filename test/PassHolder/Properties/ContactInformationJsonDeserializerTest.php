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
        $this->assertTrue($expected->sameValueAs($actual));
    }

    /**
     * @test
     */
    public function it_should_throw_an_error_when_a_invalid_email_address_is_provided()
    {
        $json = file_get_contents(__DIR__ . '/../data/properties/contact-information-invalid-email.json');

        $this->setExpectedException(EmailAddressInvalidException::class);

        $this->deserializer->deserialize(new StringLiteral($json));
    }

    /**
     * @test
     */
    public function it_should_allow_to_leave_email_address_property_empty()
    {
        $expected = new ContactInformation();

        $json = file_get_contents(__DIR__ . '/../data/properties/contact-information-with-empty-properties.json');

        $actual = $this->deserializer->deserialize(new StringLiteral($json));

        $this->assertTrue($expected->sameValueAs($actual));
    }
}
