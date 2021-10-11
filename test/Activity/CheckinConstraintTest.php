<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Event_CultureEvent;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\DateTime\Hour;
use ValueObjects\DateTime\Minute;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Second;
use ValueObjects\DateTime\Time;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinConstraintTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var checkinConstraint
     */
    protected $checkinConstraint;

    /**
     * @var boolean
     */
    protected $allowed;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var StringLiteral
     */
    protected $reason;

    public function setUp()
    {
        $this->allowed = false;

        $this->startDate = new DateTime(
            new Date(
                Year::fromNative(2015),
                Month::getByName('SEPTEMBER'),
                MonthDay::fromNative(1)
            ),
            new Time(
                Hour::fromNative(9),
                Minute::fromNative(0),
                Second::fromNative(0)
            )
        );

        $this->endDate = new DateTime(
            new Date(
                Year::fromNative(2016),
                Month::getByName('MARCH'),
                MonthDay::fromNative(1)
            ),
            new Time(
                Hour::fromNative(16),
                Minute::fromNative(0),
                Second::fromNative(0)
            )
        );

        $this->reason = new StringLiteral('INVALID_DATE_TIME');
        $this->checkinConstraint = new checkinConstraint(
            $this->allowed,
            $this->startDate,
            $this->endDate
        );
        $this->checkinConstraint = $this->checkinConstraint->withReason($this->reason);
    }

    /**
     * @test
     */
    public function it_can_return_the_data_from_the_constructor()
    {
        $this->assertEquals($this->allowed, $this->checkinConstraint->getAllowed());
        $this->assertEquals($this->startDate, $this->checkinConstraint->getStartDate());
        $this->assertEquals($this->endDate, $this->checkinConstraint->getEndDate());
        $this->assertEquals($this->reason, $this->checkinConstraint->getReason());
    }

    /**
     * @test
     */
    public function it_can_be_json_encoded()
    {
        $this->assertJsonEquals(
            json_encode($this->checkinConstraint),
            'Activity/data/checkin_constraint.json'
        );
    }

    /**
     * @test
     */
    public function it_correctly_converts_checkinStartDate_and_checkinEndDate(): void
    {
        $event = new CultureFeed_Uitpas_Event_CultureEvent();
        $event->checkinAllowed = true;

        // culturefeed-php converts the datetime in the XML to a timestamp using strtotime(), so we should do to copy
        // the actual behavior.
        $event->checkinStartDate = strtotime('2021-10-31T23:00:00+01:00');
        $event->checkinEndDate = strtotime('2022-01-30T23:59:59+01:00');

        // After converting the datetime in the XML to a unix timestamp, then a ValueObjects\DateTime\DateTime object,
        // then a native \DateTime object, and then finally a string in a JSON property, it should be the same as before
        // in the XML.
        $expected = [
            'allowed' => true,
            'startDate' => '2021-10-31T23:00:00+01:00',
            'endDate' => '2022-01-30T23:59:59+01:00',
        ];

        $actual = CheckinConstraint::fromCultureFeedUitpasEvent($event)->jsonSerialize();

        $this->assertEquals($expected, $actual);
    }
}
