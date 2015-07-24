<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\DateTime;
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
        $checkinStartDate = \DateTime::createFromFormat('U', 1441098000);
        $checkinEndDate = \DateTime::createFromFormat('U', 1456848000);

        $this->allowed = false;
        $this->startDate = DateTime::fromNativeDateTime($checkinStartDate);
        $this->endDate = DateTime::fromNativeDateTime($checkinEndDate);
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
