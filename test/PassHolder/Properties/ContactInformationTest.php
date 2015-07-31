<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class ContactInformationTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var EmailAddress
     */
    protected $email;

    /**
     * @var StringLiteral
     */
    protected $telephoneNumber;

    /**
     * @var StringLiteral
     */
    protected $mobileNumber;

    /**
     * @var ContactInformation
     */
    protected $contactInformation;

    public function setUp()
    {
        $this->email = new EmailAddress('zyrani_.hotmail.com@mailinator.com');
        $this->telephoneNumber = new StringLiteral('0488694231');
        $this->mobileNumber = new StringLiteral('0499748596');

        $this->contactInformation = (new ContactInformation())
            ->withEmail($this->email)
            ->withTelephoneNumber($this->telephoneNumber)
            ->withMobileNumber($this->mobileNumber);
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->contactInformation);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/contact-information-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_optional_properties_from_json()
    {
        $contactInformation = (new ContactInformation())
            ->withEmail($this->email);

        $json = json_encode($contactInformation);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/contact-information-incomplete.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassHolder->telephone = '0488694231';
        $cfPassHolder->gsm = '0499748596';

        $contactInfo = ContactInformation::fromCultureFeedPassHolder($cfPassHolder);
        $this->assertJsonEquals(json_encode($contactInfo), 'PassHolder/data/properties/contact-information-complete.json');
    }
}
