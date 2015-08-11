<?php

namespace CultuurNet\UiTPASBeheer\Activity;

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
}
