<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Identity\Identity;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedResultSet;
use CultuurNet\UiTPASBeheer\PassHolder\Search\Query;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\DateTime\Date;
use ValueObjects\Identity\UUID;
use CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

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
     * @var \CultureFeed_Uitpas_Counter_Employee
     */
    protected $counter;

    /**
     * @var PassHolderService
     */
    protected $service;

    public function setUp()
    {
        date_default_timezone_set('Europe/Brussels');

        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->counter = new \CultureFeed_Uitpas_Counter_Employee();
        $this->counter->consumerKey = $this->counterConsumerKey->toNative();

        $this->service = new PassHolderService(
            $this->uitpas,
            $this->counterConsumerKey,
            $this->counter
        );
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
        $cfPassHolder = $this->createCFPassHolderBaseForModification();
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
        $cfPassHolder->moreInfo = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed haec omittamus; Ecce aliud simile dissimile. Aliter homines, aliter philosophos loqui putas oportere? Cum ageremus, inquit, vitae beatum et eundem supremum diem, scribebamus haec. Propter nos enim illam, non propter eam nosmet ipsos diligimus.';

        $this->uitpas->expects($this->once())
            ->method('updatePassholder')
            ->with($cfPassHolder, $this->counterConsumerKey);

        $this->service->update($uitpasNumber, $passHolder);
    }

    /**
     * @return \CultureFeed_Uitpas_Passholder
     */
    public function createCFPassHolderBaseForModification()
    {
        $cfPassholder = new \CultureFeed_Uitpas_Passholder();
        $cfPassholder->toPostDataKeepEmptySecondName();
        $cfPassholder->toPostDataKeepEmptyEmail();
        $cfPassholder->toPostDataKeepEmptyMoreInfo();

        return $cfPassholder;
    }

    /**
     * @test
     */
    public function it_should_register_a_new_passholder_linked_to_a_given_UiTPAS_number_and_return_a_UUID()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passholder = $this->getCompletePassHolder(Gender::FEMALE());

        $cfPassholder = $this->createCFPassHolderBaseForModification();
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
        $cfPassholder->moreInfo = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed haec omittamus; Ecce aliud simile dissimile. Aliter homines, aliter philosophos loqui putas oportere? Cum ageremus, inquit, vitae beatum et eundem supremum diem, scribebamus haec. Propter nos enim illam, non propter eam nosmet ipsos diligimus.';

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

        $cfPassholder = $this->createCFPassHolderBaseForModification();
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

        $cfPassholder = $this->createCFPassHolderBaseForModification();
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
        $cfPassholder->kansenStatuutEndDate = '11855890800';
        $cfPassholder->voucherNumber = 'i-am-a-voucher';
        $cfPassholder->moreInfo = 'This is a kansenstatuut remark, please don\'t read me';

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

        $cfPassholder = $this->createCFPassHolderBaseForModification();
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
        $cfPassholder->kansenStatuutEndDate = '11855890800';

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

    /**
     * @test
     */
    public function it_fails_on_passholders_without_uitpasses_when_searching()
    {
        $numbers = (new UiTPASNumberCollection())
            ->with(new UiTPASNumber('0930000801207'))
            ->with(new UiTPASNumber('3330047460116'))
            ->with(new UiTPASNumber('0930000802619'));

        $expectedCFUitpasSearchOptions = new CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        $expectedCFUitpasSearchOptions->balieConsumerKey = 'key';
        $expectedCFUitpasSearchOptions->max = 10;
        $expectedCFUitpasSearchOptions->start = 0;
        $expectedCFUitpasSearchOptions->uitpasNumber = array(
            '0930000801207',
            '3330047460116',
            '0930000802619',
        );

        $passHolderA = new \CultureFeed_Uitpas_Passholder();
        $passHolderA->firstName = 'John';
        $passHolderA->name = 'Doe';
        $passHolderA->gender = 'MALE';
        $passHolderA->street = 'Foo 11';
        $passHolderA->city = 'Leuven';
        $passHolderA->postalCode = '3000';

        $passHolderB = new \CultureFeed_Uitpas_Passholder();
        $passHolderB->firstName = 'Jane';
        $passHolderB->name = 'Doe';
        $passHolderB->gender = 'FEMALE';
        $passHolderA->street = 'Foo 12';
        $passHolderA->city = 'Leuven';
        $passHolderA->postalCode = '3000';

        $cfResults = new \CultureFeed_Uitpas_Passholder_ResultSet(
            2,
            array(
                $passHolderA,
                $passHolderB,
            )
        );

        $this->uitpas->expects($this->once())
            ->method('searchPassholders')
            ->with($expectedCFUitpasSearchOptions)
            ->willReturn($cfResults);

        $this->setExpectedException(
            \LogicException::class,
            'PassHolder returned by search has not a single uitpas'
        );

        $this->service->search(
            (new Query())->withUiTPASNumbers($numbers)
        );
    }

    /**
     * @test
     */
    public function it_constructs_identities_based_on_the_uitpas_numbers_searched_for()
    {
        $numbers = (new UiTPASNumberCollection())
            ->with(new UiTPASNumber('0930000801207'))
            ->with(new UiTPASNumber('3330047460116'))
            ->with(new UiTPASNumber('0930000802619'));

        $expectedCFUitpasSearchOptions = new CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        $expectedCFUitpasSearchOptions->balieConsumerKey = 'key';
        $expectedCFUitpasSearchOptions->max = 20;
        $expectedCFUitpasSearchOptions->start = 40;
        $expectedCFUitpasSearchOptions->uitpasNumber = array(
            '0930000801207',
            '3330047460116',
            '0930000802619',
        );

        $invalidNumbers = array('0930000801207');

        $passHolderACardSystemB = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $passHolderACardSystemB->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            2,
            'Card system B'
        );
        $passHolderACardSystemB->currentCard = new \CultureFeed_Uitpas_Passholder_Card();
        $passHolderACardSystemB->currentCard->kansenpas = false;
        $passHolderACardSystemB->currentCard->status = 'ACTIVE';
        $passHolderACardSystemB->currentCard->uitpasNumber = '0930000803104';
        $passHolderACardSystemB->currentCard->type = 'CARD';

        $passHolderACardSystemA = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $passHolderACardSystemA->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            1,
            'Card system A'
        );
        $passHolderACardSystemA->currentCard = new \CultureFeed_Uitpas_Passholder_Card();
        $passHolderACardSystemA->currentCard->kansenpas = false;
        $passHolderACardSystemA->currentCard->status = 'ACTIVE';
        $passHolderACardSystemA->currentCard->uitpasNumber = '0930000802619';
        $passHolderACardSystemA->currentCard->type = 'CARD';

        $cfPassHolderA = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolderA->firstName = 'John';
        $cfPassHolderA->name = 'Doe';
        $cfPassHolderA->gender = 'MALE';
        $cfPassHolderA->street = 'Foo 11';
        $cfPassHolderA->city = 'Leuven';
        $cfPassHolderA->postalCode = '3000';
        $cfPassHolderA->cardSystemSpecific = array(
            $passHolderACardSystemA,
            $passHolderACardSystemB,
        );

        // Passholder B does not have any uitpasses that match the passes we searched
        // for. We expect the first uitpas to which the counter has access to be used
        // as the preferred card in the identity.
        $passHolderBCardSystemB = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $passHolderBCardSystemB->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            2,
            'Card system B'
        );
        $passHolderBCardSystemB->currentCard = new \CultureFeed_Uitpas_Passholder_Card();
        $passHolderBCardSystemB->currentCard->kansenpas = false;
        $passHolderBCardSystemB->currentCard->status = 'ACTIVE';
        $passHolderBCardSystemB->currentCard->uitpasNumber = '1000000035419';
        $passHolderBCardSystemB->currentCard->type = 'CARD';

        $passHolderBCardSystemA = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $passHolderBCardSystemA->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            1,
            'Card system A'
        );
        $passHolderBCardSystemA->currentCard = new \CultureFeed_Uitpas_Passholder_Card();
        $passHolderBCardSystemA->currentCard->kansenpas = false;
        $passHolderBCardSystemA->currentCard->status = 'ACTIVE';
        $passHolderBCardSystemA->currentCard->uitpasNumber = '3330047460116';
        $passHolderBCardSystemA->currentCard->type = 'CARD';

        $cfPassHolderB = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolderB->firstName = 'Jane';
        $cfPassHolderB->name = 'Doe';
        $cfPassHolderB->gender = 'FEMALE';
        $cfPassHolderB->street = 'Foo 12';
        $cfPassHolderB->city = 'Leuven';
        $cfPassHolderB->postalCode = '3000';
        $cfPassHolderB->cardSystemSpecific = array(
            $passHolderBCardSystemB,
            $passHolderBCardSystemA,
        );

        $cfResults = new \CultureFeed_Uitpas_Passholder_ResultSet(
            2,
            array(
                $cfPassHolderA,
                $cfPassHolderB,
            ),
            $invalidNumbers
        );

        $this->uitpas->expects($this->once())
            ->method('searchPassholders')
            ->with($expectedCFUitpasSearchOptions)
            ->willReturn($cfResults);

        $results = $this->service->search(
            (new Query())
                ->withUiTPASNumbers($numbers)
                ->withPagination(
                    new Integer(3),
                    new Integer(20)
                )
        );

        $identityA = new Identity(
            new UiTPAS(
                new UiTPASNumber('0930000802619'),
                UiTPASStatus::ACTIVE(),
                UiTPASType::CARD(),
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('Card system A')
                )
            )
        );

        $identityA = $identityA->withPassHolder(
            PassHolder::fromCultureFeedPassHolder($cfPassHolderA)
        );

        $identityB = new Identity(
            new UiTPAS(
                new UiTPASNumber('3330047460116'),
                UiTPASStatus::ACTIVE(),
                UiTPASType::CARD(),
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('Card system A')
                )
            )
        );
        $identityB = $identityB->withPassHolder(
            PassHolder::fromCultureFeedPassHolder($cfPassHolderB)
        );

        $expectedResults = new PagedResultSet(
            new Integer(2),
            [
                $identityA,
                $identityB,
            ]
        );

        $expectedResults = $expectedResults->withInvalidUiTPASNumbers(
            (new UiTPASNumberCollection())
                ->with(new UiTPASNumber('0930000801207'))
        );

        $this->assertEquals(
            $expectedResults,
            $results
        );
    }
}
