<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\Identity\UUID;

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
        date_default_timezone_set('Europe/Brussels');

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

    public function updatePassHolderData()
    {
        // Genders are a special case. Normally the gender is indicated by
        // 'FEMALE' and 'MALE', when updating a passholder though the values
        // 'F' and 'M' are expected to be used.
        return [
            [Gender::FEMALE(), 'F'],
            [Gender::MALE(), 'M'],
        ];
    }

    /**
     * @test
     * @dataProvider updatePassHolderData
     * @param Gender $gender
     * @param string $expectedCfPassHolderGender
     */
    public function it_updates_a_given_passholder(
        Gender $gender,
        $expectedCfPassHolderGender
    ) {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passHolder = $this->getCompletePassHolder($gender);

        // Picture and points can not be updated with this call,
        // so they should not be set.
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->toPostDataKeepEmptySecondName();
        $cfPassHolder->uitpasNumber = $uitpasNumberValue;
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->dateOfBirth = 211417200;
        $cfPassHolder->street = 'Rue Perdue 101 /0003';
        $cfPassHolder->placeOfBirth = 'Casablanca';
        $cfPassHolder->secondName = 'Zoni';
        $cfPassHolder->gender = $expectedCfPassHolderGender;
        $cfPassHolder->inszNumber = '93051822361';
        $cfPassHolder->nationality = 'Maroc';
        $cfPassHolder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassHolder->telephone = '0488694231';
        $cfPassHolder->gsm = '0499748596';
        $cfPassHolder->smsPreference = 'NOTIFICATION_SMS';
        $cfPassHolder->emailPreference = 'ALL_MAILS';

        $this->uitpas->expects($this->once())
            ->method('updatePassholder')
            ->with($cfPassHolder, $this->counterConsumerKey);

        $this->service->update($uitpasNumber, $passHolder);
    }

    /**
     * @test
     */
    public function it_should_register_a_new_passholder_linked_to_a_given_UiTPAS_number_and_return_a_UUID()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passholder = $this->getCompletePassHolder(Gender::FEMALE());

        $cfPassholder = new \CultureFeed_Uitpas_Passholder();
        $cfPassholder->toPostDataKeepEmptySecondName();
        $cfPassholder->uitpasNumber = $uitpasNumberValue;
        $cfPassholder->name = 'Zyrani';
        $cfPassholder->firstName = 'Layla';
        $cfPassholder->postalCode = '1090';
        $cfPassholder->city = 'Jette (Brussel)';
        $cfPassholder->dateOfBirth = 211417200;
        $cfPassholder->street = 'Rue Perdue 101 /0003';
        $cfPassholder->placeOfBirth = 'Casablanca';
        $cfPassholder->secondName = 'Zoni';
        $cfPassholder->gender = 'F';
        $cfPassholder->inszNumber = '93051822361';
        $cfPassholder->nationality = 'Maroc';
        $cfPassholder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassholder->telephone = '0488694231';
        $cfPassholder->gsm = '0499748596';
        $cfPassholder->smsPreference = 'NOTIFICATION_SMS';
        $cfPassholder->emailPreference = 'ALL_MAILS';

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willThrowException(new \CultureFeed_Exception('Not found.', 404));

        $this->uitpas->expects($this->once())
            ->method('createPassholder')
            ->with($cfPassholder)
            ->willReturn('de305d54-75b4-431b-adb2-eb6b9e546014');

        $newPassholderUUID = $this->service->register(
            $uitpasNumber,
            $passholder
        );

        $expectedUUID = new UUID('de305d54-75b4-431b-adb2-eb6b9e546014');

        $this->assertEquals($expectedUUID, $newPassholderUUID);
    }

    /**
     * @test
     */
    public function it_should_not_try_to_register_a_new_passholder_with_an_already_used_UiTPAS_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passholder = $this->getCompletePassHolder(Gender::FEMALE());

        $cfPassholder = new \CultureFeed_Uitpas_Passholder();
        $cfPassholder->toPostDataKeepEmptySecondName();
        $cfPassholder->uitpasNumber = $uitpasNumberValue;
        $cfPassholder->name = 'Zyrani';
        $cfPassholder->firstName = 'Layla';
        $cfPassholder->postalCode = '1090';
        $cfPassholder->city = 'Jette (Brussel)';
        $cfPassholder->dateOfBirth = 211417200;
        $cfPassholder->street = 'Rue Perdue 101 /0003';
        $cfPassholder->placeOfBirth = 'Casablanca';
        $cfPassholder->secondName = 'Zoni';
        $cfPassholder->gender = 'FEMALE';
        $cfPassholder->inszNumber = '93051822361';
        $cfPassholder->nationality = 'Maroc';
        $cfPassholder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassholder->telephone = '0488694231';
        $cfPassholder->gsm = '0499748596';
        $cfPassholder->smsPreference = 'NOTIFICATION_SMS';
        $cfPassholder->emailPreference = 'ALL_MAILS';

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willReturn($cfPassholder);

        $this->setExpectedException(UiTPASNumberAlreadyUsedException::class);

        $this->service->register(
            $uitpasNumber,
            $passholder
        );
    }

    /**
     * @test
     */
    public function it_should_provide_additional_kansenstatuut_info_when_registering_a_kansenpas()
    {
        $uitpasNumberValue = '0930000125615';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $kansenstatuut = new KansenStatuut(
            Date::fromNativeDateTime(new \DateTime('2345-09-13'))
        );
        $kansenstatuut = $kansenstatuut->withRemarks(
            new Remarks('This is a kansenstatuut remark, please don\'t read me')
        );

        $passholder = $this->getCompletePassHolder(Gender::FEMALE());

        $cfPassholder = new \CultureFeed_Uitpas_Passholder();
        $cfPassholder->toPostDataKeepEmptySecondName();
        $cfPassholder->uitpasNumber = $uitpasNumberValue;
        $cfPassholder->name = 'Zyrani';
        $cfPassholder->firstName = 'Layla';
        $cfPassholder->postalCode = '1090';
        $cfPassholder->city = 'Jette (Brussel)';
        $cfPassholder->dateOfBirth = 211417200;
        $cfPassholder->street = 'Rue Perdue 101 /0003';
        $cfPassholder->placeOfBirth = 'Casablanca';
        $cfPassholder->secondName = 'Zoni';
        $cfPassholder->gender = 'F';
        $cfPassholder->inszNumber = '93051822361';
        $cfPassholder->nationality = 'Maroc';
        $cfPassholder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassholder->telephone = '0488694231';
        $cfPassholder->gsm = '0499748596';
        $cfPassholder->smsPreference = 'NOTIFICATION_SMS';
        $cfPassholder->emailPreference = 'ALL_MAILS';
        $cfPassholder->kansenStatuut = true;
        $cfPassholder->kansenStatuutEndDate = '2345-09-13T00:00:00+01:00';
        $cfPassholder->voucherNumber = 'i-am-a-voucher';

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willThrowException(new \CultureFeed_Exception('Not found.', 404));

        $this->uitpas->expects($this->once())
            ->method('createPassholder')
            ->with($cfPassholder)
            ->willReturn('de305d54-75b4-431b-adb2-eb6b9e546014');

        $this->service->register(
            $uitpasNumber,
            $passholder,
            new VoucherNumber('i-am-a-voucher'),
            $kansenstatuut
        );
    }

    /**
     * @test
     */
    public function it_should_not_allow_registration_of_a_kansenpas_without_kansenstatuut_info()
    {
        $uitpasNumberValue = '0930000125615';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passholder = $this->getCompletePassHolder(Gender::FEMALE());

        $cfPassholder = new \CultureFeed_Uitpas_Passholder();
        $cfPassholder->toPostDataKeepEmptySecondName();
        $cfPassholder->uitpasNumber = $uitpasNumberValue;
        $cfPassholder->name = 'Zyrani';
        $cfPassholder->firstName = 'Layla';
        $cfPassholder->postalCode = '1090';
        $cfPassholder->city = 'Jette (Brussel)';
        $cfPassholder->dateOfBirth = 211417200;
        $cfPassholder->street = 'Rue Perdue 101 /0003';
        $cfPassholder->placeOfBirth = 'Casablanca';
        $cfPassholder->secondName = 'Zoni';
        $cfPassholder->gender = 'F';
        $cfPassholder->inszNumber = '93051822361';
        $cfPassholder->nationality = 'Maroc';
        $cfPassholder->email = 'zyrani_.hotmail.com@mailinator.com';
        $cfPassholder->telephone = '0488694231';
        $cfPassholder->gsm = '0499748596';
        $cfPassholder->smsPreference = 'NOTIFICATION_SMS';
        $cfPassholder->emailPreference = 'ALL_MAILS';
        $cfPassholder->kansenStatuut = true;
        $cfPassholder->kansenStatuutEndDate = '2345-09-13T00:00:00+01:00';

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willThrowException(new \CultureFeed_Exception('Not found.', 404));

        $this->uitpas->expects($this->never())
            ->method('createPassholder');

        $this->setExpectedException(\InvalidArgumentException::class);

        $this->service->register(
            $uitpasNumber,
            $passholder
        );
    }
}
