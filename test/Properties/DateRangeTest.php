<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Date
     */
    protected $from;

    /**
     * @var Date
     */
    protected $to;

    /**
     * @var DateRange
     */
    protected $range;

    public function setUp()
    {
        $this->from = new Date(
            new Year('2015'),
            Month::get('October'),
            new MonthDay('1')
        );

        $this->to = new Date(
            new Year('2016'),
            Month::get('November'),
            new MonthDay('30')
        );

        $this->range = new DateRange($this->from, $this->to);
    }

    /**
     * @test
     */
    public function it_returns_both_dates()
    {
        $this->assertEquals(
            $this->from,
            $this->range->getFrom()
        );

        $this->assertEquals(
            $this->to,
            $this->range->getTo()
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_the_start_date_is_after_the_end_date()
    {
        $this->setExpectedException(
            InvalidDateRangeException::class,
            'Invalid date range 2016-11-30 - 2015-10-01. Start date should not be later than end date.'
        );
        new DateRange($this->to, $this->from);
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->range);
        $this->assertJsonEquals($json, 'Properties/data/date-range.json');
    }
}
