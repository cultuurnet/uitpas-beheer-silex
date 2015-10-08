<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use Guzzle\Stream\StreamInterface;
use ValueObjects\StringLiteral\StringLiteral;

class ExpenseReportDownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StreamInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stream;

    /**
     * @var StringLiteral
     */
    protected $contentTypeHeader;

    /**
     * @var StringLiteral
     */
    protected $contentDispositionHeader;

    /**
     * @var ExpenseReportDownload
     */
    protected $download;

    public function setUp()
    {
        $this->stream = $this->getMock(StreamInterface::class);
        $this->contentTypeHeader = new StringLiteral('application/x-zip-compressed');
        $this->contentDispositionHeader = new StringLiteral('attachment; filename=download.zip');

        $this->download = new ExpenseReportDownload(
            $this->stream,
            $this->contentTypeHeader,
            $this->contentDispositionHeader
        );
    }

    /**
     * @test
     */
    public function it_returns_the_download_stream()
    {
        $this->assertEquals(
            $this->stream,
            $this->download->getStream()
        );
    }

    /**
     * @test
     */
    public function it_returns_the_headers_as_an_associative_array()
    {
        $expected = [
            'Content-Type' => $this->contentTypeHeader->toNative(),
            'Content-Disposition' => $this->contentDispositionHeader->toNative(),
        ];

        $actual = $this->download->getHeaders();

        $this->assertEquals($expected, $actual);
    }
}
