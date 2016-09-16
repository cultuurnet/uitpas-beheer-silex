<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Price;

use CultuurNet\UiTPASBeheer\UiTPAS\Properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\Properties\VoucherType;
use ValueObjects\Money\Currency;
use ValueObjects\Money\Money;
use ValueObjects\Number\Integer;
use ValueObjects\Person\Age;
use ValueObjects\StringLiteral\StringLiteral;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_keeps_track_of_uitpas_pricing_info()
    {
        $price = new Money(new Integer(500), Currency::fromNative('EUR'));

        $uitpasPrice = new Price($price, true);

        $ageRange = new AgeRange(new Age(5), new Age(10));
        $uitpasPrice = $uitpasPrice->withAgeRange($ageRange);

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

        $uitpasPrice = Price::fromCultureFeedUiTPASPrice($culturefeedPrice);

        $expectedPrice = new Money(new Integer(500), Currency::fromNative('EUR'));

        $this->assertEquals($expectedPrice, $uitpasPrice->getPrice());
        $this->assertInstanceOf(AgeRange::class, $uitpasPrice->getAgeRange());
        $this->assertEquals(true, $uitpasPrice->isKansenStatuut());
    }

    /**
     * @test
     */
    public function it_can_extract_info_from_culturefeed_pricing_data_without_age()
    {
        $culturefeedPrice = new \CultureFeed_Uitpas_Passholder_UitpasPrice();
        $culturefeedPrice->id = 'id&t';
        $culturefeedPrice->price = 5.00;
        $culturefeedPrice->kansenStatuut = true;

        $ageRange = new \CultureFeed_Uitpas_Passholder_AgeRange();
        $culturefeedPrice->ageRange = $ageRange;

        $uitpasPrice = Price::fromCultureFeedUiTPASPrice($culturefeedPrice);

        $this->assertNull($uitpasPrice->getAgeRange());
    }

    /**
     * @test
     */
    public function it_can_format_uitpas_pricing_so_it_can_be_serialized_as_json()
    {
        $price = new Money(new Integer(500), Currency::fromNative('EUR'));

        $uitpasPrice = new Price($price, true);

        $ageRange = new AgeRange(new Age(5), new Age(10));
        $uitpasPrice = $uitpasPrice->withAgeRange($ageRange);

        // voucherInfo is optional but we want to make sure it's formatted correctly
        $voucherType = new VoucherType(
            new StringLiteral('voucher one'),
            new StringLiteral('voucher prefix')
        );
        $uitpasPrice = $uitpasPrice->withVoucherType($voucherType);

        $jsonData = json_encode($uitpasPrice);
        $jsonData = json_decode($jsonData, true);
        $expectedJsonData = [
            "price" => 5,
            "kansenStatuut" => true,
            "voucherType" => [
                "name" => "voucher one",
                "prefix" => "voucher prefix",
            ],
            "ageRange" => [
                "from" => 5,
                "to" => 10,
            ],
        ];

        $this->assertEquals($expectedJsonData, $jsonData);
    }

    /**
     * @test
     */
    public function it_can_format_uitpas_pricing_without_age_so_it_can_be_serialized_as_json()
    {
        $price = new Money(new Integer(500), Currency::fromNative('EUR'));

        $uitpasPrice = new Price($price, true);

        $jsonData = json_encode($uitpasPrice);
        $jsonData = json_decode($jsonData, true);
        $expectedJsonData = [
          "price" => 5,
          "kansenStatuut" => true,
        ];

        $this->assertEquals($expectedJsonData, $jsonData);
    }
}
