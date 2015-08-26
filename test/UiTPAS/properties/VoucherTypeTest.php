<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Properties;

use ValueObjects\StringLiteral\StringLiteral;

class VoucherTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_keeps_track_of_a_voucher_name_and_prefix()
    {
        $voucherType = new VoucherType(
            new StringLiteral('A voucher name'),
            new StringLiteral('182')
        );

        $this->assertEquals(new StringLiteral('A voucher name'), $voucherType->getName());
        $this->assertEquals(new StringLiteral('182'), $voucherType->getPrefix());
    }

    /**
     * @test
     */
    public function it_should_serialize_voucher_types_in_a_json_friendly_format()
    {
        $expectedJsonData = [
            "name" => "A voucher name",
            "prefix" => "182",
        ];

        $voucherType = new VoucherType(
            new StringLiteral('A voucher name'),
            new StringLiteral('182')
        );

        $this->assertEquals($expectedJsonData, $voucherType->jsonSerialize());
    }
}
