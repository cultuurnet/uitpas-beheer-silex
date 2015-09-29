<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASServiceTest extends \PHPUnit_Framework_TestCase
{
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
    }

    /**
     * @test
     */
    public function it_blocks_and_returns_the_blocked_uitpas()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');

        $cfUpdatedCard = new \CultureFeed_Uitpas_CardInfo();
        $cfUpdatedCard->status = 'BLOCKED';
        $cfUpdatedCard->type = 'KEY';
        $cfUpdatedCard->uitpasNumber = $uitpasNumber->toNative();
        $cfUpdatedCard->cardSystem = new \CultureFeed_Uitpas_CardSystem(7, 'UiTPAS Regio Brabant');

        $cfCardQuery = new \CultureFeed_Uitpas_CardInfoQuery();
        $cfCardQuery->uitpasNumber = $uitpasNumber->toNative();

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
            ->method('blockUitpas')
            ->with($uitpasNumber->toNative());

        $this->api->expects($this->once())
            ->method('getCard')
            ->with($cfCardQuery)
            ->willReturn($cfUpdatedCard);

        $actualUitpas = $this->service->block($uitpasNumber);

        $this->assertEquals($expectedUitpas, $actualUitpas);
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
