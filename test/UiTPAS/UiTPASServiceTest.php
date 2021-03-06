<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\Registration;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\RegistrationTestDataTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASServiceTest extends \PHPUnit_Framework_TestCase
{
    use RegistrationTestDataTrait;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var UiTPASService
     */
    protected $service;

    public function setUp()
    {
        $this->api = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('counter-key');

        $this->service = new UiTPASService(
            $this->api,
            $this->counterConsumerKey
        );

        date_default_timezone_set('Europe/Brussels');
    }

    /**
     * @test
     */
    public function it_blocks_a_given_uitpas_by_uitpas_number()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');

        $this->api->expects($this->once())
            ->method('blockUitpas')
            ->with($uitpasNumber->toNative());

        $this->service->block($uitpasNumber);
    }

    /**
     * @test
     */
    public function it_returns_the_uitpas_for_a_given_uitpas_number()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');

        $cfUpdatedCard = new \CultureFeed_Uitpas_CardInfo();
        $cfUpdatedCard->status = 'BLOCKED';
        $cfUpdatedCard->type = 'KEY';
        $cfUpdatedCard->uitpasNumber = $uitpasNumber->toNative();
        $cfUpdatedCard->cardSystem = new \CultureFeed_Uitpas_CardSystem(7, 'UiTPAS Regio Brabant');

        $cfCardQuery = new \CultureFeed_Uitpas_CardInfoQuery();
        $cfCardQuery->uitpasNumber = $uitpasNumber->toNative();
        $cfCardQuery->balieConsumerKey = $this->counterConsumerKey->toNative();

        $expectedUitpas = new UiTPAS(
            $uitpasNumber,
            UiTPASStatus::BLOCKED(),
            UiTPASType::KEY(),
            new CardSystem(
                new CardSystemId('7'),
                new StringLiteral('UiTPAS Regio Brabant')
            )
        );

        $this->api->expects($this->once())
            ->method('getCard')
            ->with($cfCardQuery)
            ->willReturn($cfUpdatedCard);

        $actualUitpas = $this->service->get($uitpasNumber);

        $this->assertEquals($expectedUitpas, $actualUitpas);
    }

    /**
     * @test
     */
    public function it_registers_an_uitpas_to_an_existing_passholder()
    {
        $uitpasNumber = new UiTPASNumber('0930000237915');
        $registration = $this->getCompleteRegistration();

        $expectedOptions = new \CultureFeed_Uitpas_Passholder_Query_RegisterUitpasOptions();
        $expectedOptions->balieConsumerKey = $this->counterConsumerKey->toNative();
        $expectedOptions->reason = PurchaseReason::LOSS_THEFT;
        $expectedOptions->uid = '5';
        $expectedOptions->voucherNumber = 'abc-123';
        $expectedOptions->kansenStatuutEndDate = 1451516400;
        $expectedOptions->uitpasNumber = '0930000237915';

        $this->api->expects($this->once())
            ->method('registerUitpas')
            ->with($expectedOptions);

        $this->service->register($uitpasNumber, $registration);
    }

    /**
     * @test
     */
    public function it_returns_the_price_for_an_uitpas_price_inquiry()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');
        $reason = PurchaseReason::FIRST_CARD();
        $dateOfBirth = new Date(
            new Year('1991'),
            Month::getByName('APRIL'),
            new MonthDay('23')
        );
        $postalCode = new StringLiteral('1000');
        $voucherNumber = new VoucherNumber('2000000113');

        $inquiry = (new Inquiry(
            $uitpasNumber,
            $reason
        ))->withDateOfBirth(
            $dateOfBirth
        )->withPostalCode(
            $postalCode
        )->withVoucherNumber(
            $voucherNumber
        );

        $cfPrice = new \CultureFeed_Uitpas_Passholder_UitpasPrice();
        $cfPrice->price = 5.00;
        $cfPrice->ageRange = new \CultureFeed_Uitpas_Passholder_AgeRange();
        $cfPrice->ageRange->ageFrom = 18;
        $cfPrice->kansenStatuut = false;

        $expectedPrice = Price::fromCultureFeedUiTPASPrice($cfPrice);

        $this->api->expects($this->once())
            ->method('getPriceByUitpas')
            ->with(
                '0930000420206',
                'FIRST_CARD',
                672357600,
                '1000',
                '2000000113',
                'counter-key'
            )
            ->willReturn($cfPrice);

        $actualPrice = $this->service->getPrice($inquiry);

        $this->assertEquals($expectedPrice, $actualPrice);
    }
}
