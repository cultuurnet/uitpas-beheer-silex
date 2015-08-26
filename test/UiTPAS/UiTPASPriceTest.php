<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\UiTPAS\properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\properties\VoucherType;
use ValueObjects\Money\Currency;
use ValueObjects\Money\Money;
use ValueObjects\Number\Integer;
use ValueObjects\Person\Age;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_keeps_track_of_uitpas_pricing_info()
    {
        $price = new Money(new Integer(500), Currency::fromNative('EUR'));
        $ageRange = new AgeRange(new Age(5), new Age(10));

        $uitpasPrice = new UiTPASPrice($price, true, $ageRange);

        // voucherInfo is optional
        $voucherType = new VoucherType(
            new StringLiteral('voucher one'),
            new StringLiteral('voucher prefix')
        );
        $uitpasPrice = $uitpasPrice->withVoucherType($voucherType);

        $this->assertEquals($price, $uitpasPrice->getPrice());
        $this->assertEquals($ageRange, $uitpasPrice->getAgeRange());
        $this->assertEquals(true, $uitpasPrice->isKansenStatuut());
        $this->assertEquals($voucherType, $uitpasPrice->getVoucherType());
    }

    /**
     * @test
     */
    public function it_can_extract_info_from_culturefeed_pricing_data()
    {
        $culturefeedPrice = new \CultureFeed_Uitpas_Passholder_UitpasPrice();
        $culturefeedPrice->id = 'id&t';
        $culturefeedPrice->price = 5.00;
        $culturefeedPrice->kansenStatuut = true;

        $voucherType = new \CultureFeed_Uitpas_Passholder_VoucherType();
        $voucherType->name = 'voucher one';
        $voucherType->prefix = '65';
        $culturefeedPrice->voucherType = $voucherType;

        $ageRange = new \CultureFeed_Uitpas_Passholder_AgeRange();
        $ageRange->ageFrom = 5;
        $ageRange->ageTo = 10;
        $culturefeedPrice->ageRange = $ageRange;

        $uitpasPrice = UiTPASPrice::fromCultureFeedUiTPASPrice($culturefeedPrice);

        $expectedPrice = new Money(new Integer(500), Currency::fromNative('EUR'));

        $this->assertEquals($expectedPrice, $uitpasPrice->getPrice());
        $this->assertInstanceOf(AgeRange::class, $uitpasPrice->getAgeRange());
        $this->assertEquals(true, $uitpasPrice->isKansenStatuut());
    }

    /**
     * @test
     */
    public function it_can_format_uitpas_pricing_so_it_can_be_serialized_as_json()
    {
        $price = new Money(new Integer(500), Currency::fromNative('EUR'));
        $ageRange = new AgeRange(new Age(5), new Age(10));
        $ageRange->overclock(
            new FrozenClock(
                new \DateTime('2015-7-24', new \DateTimeZone('Europe/Brussels'))
            )
        );

        $uitpasPrice = new UiTPASPrice($price, true, $ageRange);

        // voucherInfo is optional but we want to make sure it's formatted correctly
        $voucherType = new VoucherType(
            new StringLiteral('voucher one'),
            new StringLiteral('voucher prefix')
        );
        $uitpasPrice = $uitpasPrice->withVoucherType($voucherType);

        $jsonData = json_encode($uitpasPrice);
        $jsonData = json_decode($jsonData, true);
        $expectedJsonData = [
            "price" => 500,
            "kansenStatuut" => true,
            "voucherType" => [
                "name" => "voucher one",
                "prefix" => "voucher prefix",
            ],
            "ageRange" => [
                "from" => [
                    "age" => 5,
                    "date" => "2010-07-24T00:00:00+02:00",
                ],
                "to" => [
                    "age" => 10,
                    "date" => "2000-07-24T00:00:00+02:00",
                ],
            ],
        ];

        $this->assertEquals($expectedJsonData, $jsonData);
    }
}
