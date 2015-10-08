<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Web\Url;

class ExpenseReportStatusTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var ExpenseReportStatus
     */
    protected $incompleteStatus;

    /**
     * @var ExpenseReportStatus
     */
    protected $completeStatus;

    /**
     * @var Url
     */
    protected $downloadUrl;

    public function setUp()
    {
        $this->incompleteStatus = ExpenseReportStatus::incomplete();

        $this->downloadUrl = Url::fromNative('http://foo.bar/download.zip');

        $this->completeStatus = ExpenseReportStatus::complete($this->downloadUrl);
    }

    /**
     * @test
     */
    public function it_returns_all_info()
    {
        $this->assertFalse($this->incompleteStatus->isComplete());
        $this->assertNull($this->incompleteStatus->getDownloadUrl());

        $this->assertTrue($this->completeStatus->isComplete());
        $this->assertEquals(
            $this->downloadUrl,
            $this->completeStatus->getDownloadUrl()
        );
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->completeStatus);
        $this->assertJsonEquals($json, 'ExpenseReport/data/expense-report-status-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_download_url_when_encoding_an_incomplete_status_to_json()
    {
        $json = json_encode($this->incompleteStatus);
        $this->assertJsonEquals($json, 'ExpenseReport/data/expense-report-status-incomplete.json');
    }
}
