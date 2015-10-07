<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\Properties\DateRange;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

class ExpenseReportInfoTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var ExpenseReportId
     */
    protected $id;

    /**
     * @var DateRange
     */
    protected $dateRange;

    /**
     * @var ExpenseReportInfo
     */
    protected $info;

    public function setUp()
    {
        $this->id = new ExpenseReportId('1052');

        $this->dateRange = new DateRange(
            new Date(
                new Year('2015'),
                Month::get('October'),
                new MonthDay('1')
            ),
            new Date(
                new Year('2016'),
                Month::get('November'),
                new MonthDay('30')
            )
        );

        $this->info = new ExpenseReportInfo(
            $this->id,
            $this->dateRange
        );
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $this->assertEquals(
            $this->id,
            $this->info->getId()
        );

        $this->assertEquals(
            $this->dateRange,
            $this->info->getDateRange()
        );
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->info);
        $this->assertJsonEquals($json, 'ExpenseReport/data/expense-report-info.json');
    }
}
