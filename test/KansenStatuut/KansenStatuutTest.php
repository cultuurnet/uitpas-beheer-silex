<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class KansenstatuutTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var Remarks
     */
    protected $remarks;

    /**
     * @var KansenStatuutStatus
     */
    protected $status;

    /**
     * @var UiTPAS
     */
    protected $uitPas;

    /**
     * @var KansenStatuut
     */
    protected $kansenStatuut;

    public function setUp()
    {
        $this->endDate = new Date(
            new Year('2015'),
            Month::getByName('DECEMBER'),
            new MonthDay('26')
        );

        $this->remarks = new Remarks('beep boop');

        $this->status = KansenStatuutStatus::IN_GRACE_PERIOD();

        $this->uitPas = new UiTPAS(
            new UiTPASNumber('0930000420206'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $this->kansenStatuut = (new KansenStatuut($this->endDate))
            ->withRemarks($this->remarks)
            ->withStatus($this->status)
            ->withUiTPAS($this->uitPas);
    }

    /**
     * @test
     */
    public function it_returns_any_properties_that_were_set_previously()
    {
        $this->assertEquals($this->endDate, $this->kansenStatuut->getEndDate());
        $this->assertEquals($this->remarks, $this->kansenStatuut->getRemarks());
        $this->assertEquals($this->status, $this->kansenStatuut->getStatus());
        $this->assertEquals($this->uitPas, $this->kansenStatuut->getUiTPAS());
    }

    /**
     * @test
     */
    public function it_can_be_encoded_to_json()
    {
        $json = json_encode($this->kansenStatuut);
        $this->assertJsonEquals($json, 'KansenStatuut/data/kansen-statuut-complete.json');
    }
}
