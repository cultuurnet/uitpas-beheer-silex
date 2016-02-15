<?php

namespace CultuurNet\UiTPASBeheer\CardSystem\Price;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
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

        $this->assertEquals($cardSystemId, $inquiry->getCardSystemId());
        $this->assertEquals($dateOfBirth, $inquiry->getDateOfBirth());
        $this->assertEquals($postalCode, $inquiry->getPostalCode());
        $this->assertEquals($voucherNumber, $inquiry->getVoucherNumber());
    }
}
