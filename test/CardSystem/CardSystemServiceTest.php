<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Price\Inquiry;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CardSystemService
     */
    protected $service;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->api = $this->getMock(\CultureFeed_Uitpas::class);

        $this->service = new CardSystemService(
            $this->api,
            new CounterConsumerKey('foo')
        );

        date_default_timezone_set('Europe/Brussels');
    }

    /**
     * @test
     */
    public function it_returns_the_price_for_a_cardsystem_price_inquiry()
    {
        $cardSystemId = new CardSystemId('4');
        $dateOfBirth = new Date(
            new Year('1991'),
            Month::getByName('APRIL'),
            new MonthDay('23')
        );
        $postalCode = new StringLiteral('1000');
        $voucherNumber = new VoucherNumber('2000000113');

        $inquiry = (new Inquiry(
            $cardSystemId
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
            ->method('getPriceForUpgrade')
            ->with(
                '4',
                672357600,
                '1000',
                '2000000113',
                'foo'
            )
            ->willReturn($cfPrice);

        $actualPrice = $this->service->getPrice($inquiry);

        $this->assertEquals($expectedPrice, $actualPrice);
    }
}
