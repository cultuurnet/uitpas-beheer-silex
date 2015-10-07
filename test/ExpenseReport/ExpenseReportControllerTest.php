<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\Properties\DateRange;
use CultuurNet\UiTPASBeheer\Properties\DateRangeJsonDeserializer;
use Guzzle\Http\EntityBody;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\Url;

class ExpenseReportControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var ExpenseReportServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var ExpenseReportApiServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @var ExpenseReportController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(ExpenseReportServiceInterface::class);
        $this->api = $this->getMock(ExpenseReportApiServiceInterface::class);

        $this->controller = new ExpenseReportController(
            $this->service,
            $this->api,
            new DateRangeJsonDeserializer()
        );
    }

    /**
     * @test
     */
    public function it_responds_info_when_generating()
    {
        $dateRangeJson = file_get_contents(__DIR__ . '/../Properties/data/date-range.json');

        $id = new ExpenseReportId('1052');

        $dateRange = new DateRange(
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

        $info = new ExpenseReportInfo(
            $id,
            $dateRange
        );

        $this->service->expects($this->once())
            ->method('generate')
            ->with($dateRange)
            ->willReturn($info);

        $response = $this->controller->generate(
            new Request([], [], [], [], [], [], $dateRangeJson)
        );
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'ExpenseReport/data/expense-report-info.json');
    }

    /**
     * @test
     */
    public function it_responds_the_status_of_a_specific_report()
    {
        $id = new ExpenseReportId('1052');

        $status = new ExpenseReportStatus(
            true,
            Url::fromNative('http://foo.bar/download.zip')
        );

        $this->service->expects($this->once())
            ->method('getStatus')
            ->with($id)
            ->willReturn($status);

        $response = $this->controller->getStatus($id->toNative());
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'ExpenseReport/data/expense-report-status-complete.json');
    }

    /**
     * @test
     */
    public function it_responds_a_stream_when_downloading()
    {
        $id = new ExpenseReportId('1052');

        $filePath = __DIR__ . '/data/financialOverview_CC De Werf _20151007_1404.zip';

        $contentType = 'application/x-zip-compressed';
        $contentDisposition = 'attachment; filename="financialOverview_CC De Werf _20151007_1404.zip"';

        $download = new ExpenseReportDownload(
            EntityBody::factory(
                fopen($filePath, 'r')
            ),
            new StringLiteral($contentType),
            new StringLiteral($contentDisposition)
        );

        $this->api->expects($this->once())
            ->method('download')
            ->with($id)
            ->willReturn($download);

        $response = $this->controller->download($id->toNative());

        $this->assertEquals(
            $contentType,
            $response->headers->get('Content-Type')
        );

        $this->assertEquals(
            $contentDisposition,
            $response->headers->get('Content-Disposition')
        );

        ob_start();
        $response->sendContent();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(
            file_get_contents($filePath),
            $content
        );
    }
}
