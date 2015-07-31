<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\INSZNumber;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceEmail;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferences;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceSMS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class PassHolderServiceTest extends \PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var string
     */
    protected $counterConsumerKey;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var PassHolderService
     */
    protected $service;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new PassHolderService($this->uitpas, $this->counterConsumerKey);
    }

    /**
     * @test
     */
    public function it_can_get_a_passholder_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->dateOfBirth = 208742400;

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willReturn($cfPassHolder);

        $expected = PassHolder::fromCultureFeedPassHolder($cfPassHolder);
        $actual = $this->service->getByUitpasNumber($uitpasNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_passholder_cannot_be_found_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willThrowException(new \CultureFeed_Exception('Not found.', 404));

        $passholder = $this->service->getByUitpasNumber($uitpasNumber);

        $this->assertNull($passholder);
    }

    /**
     * @test
     */
    public function it_updates_a_given_passholder()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passHolder = $this->getCompletePassHolder();

        // Picture and points can not be updated with this call,
        // so they should not be set.
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->uitpasNumber = $uitpasNumberValue;
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->dateOfBirth = 211420800;
        $cfPassHolder->street = 'Rue Perdue 101 /0003';
        $cfPassHolder->placeOfBirth = 'Casablanca';
        $cfPassHolder->secondName = 'Zoni';
        $cfPassHolder->gender = 'FEMALE';
        $cfPassHolder->inszNumber = '93051822361';
        $cfPassHolder->nationality = 'Maroc';
        $cfPassHolder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassHolder->telephone = '0488694231';
        $cfPassHolder->gsm = '0499748596';
        $cfPassHolder->smsPreference = 'NO_SMS';
        $cfPassHolder->emailPreference = 'ALL_MAILS';

        $this->uitpas->expects($this->once())
            ->method('updatePassholder')
            ->with($cfPassHolder, $this->counterConsumerKey);

        $this->service->update($uitpasNumber, $passHolder);
    }
}
