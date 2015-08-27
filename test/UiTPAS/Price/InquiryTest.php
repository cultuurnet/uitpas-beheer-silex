<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Price;

use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class InquiryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_all_properties()
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

        $this->assertEquals($uitpasNumber, $inquiry->getUiTPASNumber());
        $this->assertEquals($reason, $inquiry->getReason());
        $this->assertEquals($dateOfBirth, $inquiry->getDateOfBirth());
        $this->assertEquals($postalCode, $inquiry->getPostalCode());
        $this->assertEquals($voucherNumber, $inquiry->getVoucherNumber());
    }
}
