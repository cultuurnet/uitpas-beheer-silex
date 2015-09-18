<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class KansenStatuutCollectionTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var KansenStatuutStatus
     */
    protected $kansenStatuutStatus;

    /**
     * @var CardSystem
     */
    protected $cardSystem;

    /**
     * @var KansenStatuut
     */
    protected $minimal;

    /**
     * @var KansenStatuut
     */
    protected $complete;

    /**
     * @var KansenStatuutCollection
     */
    protected $collection;

    /**
     * @test
     */
    public function setUp()
    {
        $this->endDate = new Date(
            new Year('2015'),
            Month::getByName('DECEMBER'),
            new MonthDay('26')
        );

        $this->kansenStatuutStatus = KansenStatuutStatus::IN_GRACE_PERIOD();

        $this->cardSystem = new CardSystem(
            new CardSystemId('999'),
            new StringLiteral('UiTPAS Regio Aalst')
        );

        $this->minimal = (new KansenStatuut($this->endDate));

        $this->complete = (new KansenStatuut($this->endDate))
            ->withStatus($this->kansenStatuutStatus)
            ->withCardSystem($this->cardSystem);

        $this->collection = (new KansenStatuutCollection())
            ->withKey('minimal', $this->minimal)
            ->withKey('complete', $this->complete);
    }

    /**
     * @test
     */
    public function it_can_be_encoded_to_json()
    {
        $json = json_encode($this->collection);
        $this->assertJsonEquals($json, 'KansenStatuut/data/kansen-statuut-collection.json');
    }
}
