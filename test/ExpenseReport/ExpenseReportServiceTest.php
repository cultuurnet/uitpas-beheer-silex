<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultureFeed_Uitpas_Calendar_Period;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\Properties\DateRange;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Web\Url;

class ExpenseReportServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpasService;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

    /**
     * @var ExpenseReportService
     */
    protected $service;

    public function setUp()
    {
        $this->uitpasService = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('abc123');
        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);

        $this->service = new ExpenseReportService(
            $this->uitpasService,
            $this->counterConsumerKey,
            $this->urlGenerator
        );
    }

    /**
     * @test
     */
    public function it_returns_a_list_of_periods()
    {
        $cfPeriods = [
            new CultureFeed_Uitpas_Calendar_Period(
                1585692000,
                1593554399
            ),
            new CultureFeed_Uitpas_Calendar_Period(
                1577833200,
                1585691999
            ),
            new CultureFeed_Uitpas_Calendar_Period(
                1569880800,
                1577833199
            ),
        ];

        $this->uitpasService->expects($this->once())
            ->method('getFinancialOverviewReportPeriods')
            ->with($this->counterConsumerKey->toNative())
            ->willReturn($cfPeriods);

        $expectedPeriods = [
            new DateRange(
                new Date(new Year(2020), Month::APRIL(), new MonthDay(1)),
                new Date(new Year(2020), Month::JUNE(), new MonthDay(30))
            ),
            new DateRange(
                new Date(new Year(2020), Month::JANUARY(), new MonthDay(1)),
                new Date(new Year(2020), Month::MARCH(), new MonthDay(31))
            ),
            new DateRange(
                new Date(new Year(2019), Month::OCTOBER(), new MonthDay(1)),
                new Date(new Year(2019), Month::DECEMBER(), new MonthDay(31))
            ),
        ];

        $actualPeriods = $this->service->getPeriods();

        $this->assertEquals($expectedPeriods, $actualPeriods);
    }

    /**
     * @test
     */
    public function it_generates_an_expense_report()
    {
        $from = \DateTime::createFromFormat(
            \DateTime::RFC3339,
            '2015-01-01T00:00:00+0100'
        );
        $to = \DateTime::createFromFormat(
            \DateTime::RFC3339,
            '2016-05-12T00:00:00+0200'
        );

        $dateRange = new DateRange(
            Date::fromNativeDateTime($from),
            Date::fromNativeDateTime($to)
        );

        $id = new ExpenseReportId('1235');

        $this->uitpasService->expects($this->once())
            ->method('generateFinancialOverviewReport')
            ->with(
                $from,
                $to,
                $this->counterConsumerKey->toNative()
            )
            ->willReturn($id->toNative());

        $expected = new ExpenseReportInfo(
            $id,
            $dateRange
        );

        $actual = $this->service->generate($dateRange);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_the_status_of_a_specific_expense_report()
    {
        $id = new ExpenseReportId('12345');
        $cfStatus = new \CultureFeed_ReportStatusInProgress();

        $this->uitpasService->expects($this->once())
            ->method('financialOverviewReportStatus')
            ->with(
                $id->toNative(),
                $this->counterConsumerKey->toNative()
            )
            ->willReturn($cfStatus);

        $expected = ExpenseReportStatus::incomplete();

        $actual = $this->service->getStatus($id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_includes_a_download_url_in_a_completed_status()
    {
        $id = new ExpenseReportId('12345');

        $cfStatus = new \CultureFeed_ReportStatusCompleted();

        $this->uitpasService->expects($this->once())
            ->method('financialOverviewReportStatus')
            ->with(
                $id->toNative(),
                $this->counterConsumerKey->toNative()
            )
            ->willReturn($cfStatus);

        $downloadUrl = Url::fromNative('http://foo.bar/download.zip');

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with(
                ExpenseReportService::DOWNLOAD_ROUTE_NAME,
                ['expenseReportId' => $id->toNative()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn((string) $downloadUrl);

        $expected = ExpenseReportStatus::complete($downloadUrl);

        $actual = $this->service->getStatus($id);

        $this->assertEquals($expected, $actual);
    }
}
