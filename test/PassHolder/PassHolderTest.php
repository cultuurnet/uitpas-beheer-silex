<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use ValueObjects\Number\Integer;

class PassHolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $passholderMinimal;

    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $passholderFull;

    public function setUp()
    {
        $this->passholderMinimal = new \CultureFeed_Uitpas_Passholder();
        $this->passholderMinimal->name = 'Zyrani';
        $this->passholderMinimal->firstName = 'Layla';
        $this->passholderMinimal->street = 'Rue Ferd 123 /0001';
        $this->passholderMinimal->postalCode = '1090';
        $this->passholderMinimal->city = 'Jette (Brussel)';
        $this->passholderMinimal->dateOfBirth = 208742400;
        $this->passholderMinimal->placeOfBirth = 'Casablance';

        $this->passholderFull = clone $this->passholderMinimal;
        $this->passholderFull->gender = 'FEMALE';
        $this->passholderFull->inszNumber = '93051822361';
        $this->passholderFull->nationality = 'Belg';
        $this->passholderFull->picture = 'R0lGODlhDwAPAKECAAAAzMzM/////wAAACwAAAAADwAPAAACIISPeQHsrZ5ModrLlN48CXF8m2iQ3YmmKqVlRtW4MLwWACH+H09wdGltaXplZCBieSBVbGVhZCBTbWFydFNhdmVyIQAAOw==';
        $this->passholderFull->points = 40;
        $this->passholderFull->email = 'zyrani_.hotmail.com@mailinator.com';
        $this->passholderFull->telephone = '0488694231';
        $this->passholderFull->gsm = '0499748596';
        $this->passholderFull->smsPreference = 'NO_SMS';
        $this->passholderFull->emailPreference = 'NOTIFICATION_MAILS';
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = clone $this->passholderFull;

        $passholder = PassHolder::fromCultureFeedPassHolder($cfPassHolder);
        $this->assertAttributeNotEmpty('inszNumber', $passholder);
        $this->assertAttributeNotEmpty('gender', $passholder);
        $this->assertAttributeNotEmpty('nationality', $passholder);
        $this->assertAttributeNotEmpty('picture', $passholder);
        $this->assertAttributeNotEmpty('points', $passholder);
        $this->assertAttributeNotEmpty('contactInformation', $passholder);
    }

    /**
     * @test
     */
    public function it_can_manage_missing_properties_while_extracting_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = clone $this->passholderMinimal;

        $passholder = PassHolder::fromCultureFeedPassHolder($cfPassHolder);
        $this->assertAttributeEmpty('inszNumber', $passholder, 'INSZNumber should not be set.');
        $this->assertAttributeEmpty('gender', $passholder, 'Gender should not be set.');
        $this->assertAttributeEmpty('nationality', $passholder, 'Nationality should not be set.');
        $this->assertAttributeEmpty('picture', $passholder, 'Picture should not be set.');
        $this->assertAttributeEquals(new Integer(0), 'points', $passholder, 'Points should not be 0 (zero).');
        $this->assertAttributeEmpty('contactInformation', $passholder, 'Contact information should not be set.');
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json()
    {
        $cfPassHolder = clone $this->passholderFull;
        $passholder = PassHolder::fromCultureFeedPassHolder($cfPassHolder);

        $data = $passholder->jsonSerialize();
        $this->assertArrayHasKey('inszNumber', $data, 'The inszNumber key is missing');
        $this->assertArrayHasKey('gender', $data, 'The gender key is missing');
        $this->assertArrayHasKey('nationality', $data, 'The nationality key is missing');
        $this->assertArrayHasKey('picture', $data, 'The picture key is missing');
        $this->assertArrayHasKey('contact', $data, 'The contact key is missing');
        $this->assertArrayHasKey('privacy', $data, 'The privacy key is missing');
        $this->assertArrayHasKey('points', $data, 'The points key is missing');
    }

    /**
     * @test
     */
    public function it_can_manage_missing_properties_while_serializing_to_json()
    {
        $cfPassHolder = clone $this->passholderMinimal;
        $passholder = PassHolder::fromCultureFeedPassHolder($cfPassHolder);

        $data = $passholder->jsonSerialize();
        $this->assertArrayNotHasKey('inszNumber', $data, 'The inszNumber key should not be set.');
        $this->assertArrayNotHasKey('gender', $data, 'The gender key should not be set.');
        $this->assertArrayNotHasKey('nationality', $data, 'The nationality key should not be set.');
        $this->assertArrayNotHasKey('picture', $data, 'The picture key should not be set.');
        $this->assertArrayNotHasKey('contact', $data, 'The contact key should not be set.');
        $this->assertArrayHasKey('privacy', $data, 'The privacy key is missing');
        $this->assertArrayHasKey('points', $data, 'The points key is missing');
    }
}
