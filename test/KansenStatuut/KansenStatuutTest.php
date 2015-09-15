<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class KansenstatuutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_requires_an_end_date_and_optional_remarks()
    {
        $endDate = Date::now();
        $remarks = new Remarks('beep boop');
        $status = KansenStatuutStatus::ACTIVE();
        $uitPas = new UiTPAS(
            new UiTPASNumber('0930000420206'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $kansenstatuut = (new KansenStatuut($endDate))
            ->withRemarks($remarks)
            ->withStatus($status)
            ->withUiTPAS($uitPas);

        $this->assertEquals($endDate, $kansenstatuut->getEndDate());
        $this->assertEquals($remarks, $kansenstatuut->getRemarks());
        $this->assertEquals($status, $kansenstatuut->getStatus());
        $this->assertEquals($uitPas, $kansenstatuut->getUiTPAS());
    }
}
