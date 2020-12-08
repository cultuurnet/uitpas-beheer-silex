<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\DefaultHttpClientFactory;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\Http\ContentDispositionHeader;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\MessageInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckInCodeServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var CheckInCodeService
     */
    protected $service;

    /**
     * @var MockPlugin
     */
    protected $mockPlugin;

    public function setUp()
    {
        $this->counterConsumerKey = new CounterConsumerKey('123546578');

        $this->service = new CheckInCodeService(
            $this->counterConsumerKey,
            'http://foo.bar/test/',
            new ConsumerCredentials(
                'key',
                'secret'
            ),
            new TokenCredentials(
                'token',
                'secret'
            )
        );

        $this->mockPlugin = new MockPlugin();

        $clientFactory = new DefaultHttpClientFactory();
        $clientFactory->addSubscriber($this->mockPlugin);

        $this->service->setHttpClientFactory($clientFactory);
    }

    /**
     * @test
     */
    public function it_can_return_a_zip_file_download_stream()
    {
        $id = new StringLiteral('132456');
        $contentDispositionHeader = 'attachment; filename="UITPAS_QR_Ancienne_Belgique.zip"';

        $responseBody = EntityBody::factory(
            fopen(__DIR__ . '/data/UITPAS_QR_Ancienne_Belgique.zip', 'r')
        );

        $mockResponse = (new Response(200))
            ->setBody($responseBody)
            ->setHeader('Content-Type', 'application/x-zip-compressed')
            ->setHeader('Content-Disposition', $contentDispositionHeader);

        $this->mockPlugin->addResponse($mockResponse);

        $expected = new CheckInCodeDownload(
            $responseBody,
            new StringLiteral('application/x-zip-compressed'),
            new ContentDispositionHeader($contentDispositionHeader)
        );

        $actual = $this->service->download($id, true);

        $this->assertEquals($expected, $actual);

        $requests = $this->mockPlugin->getReceivedRequests();
        $this->assertCount(1, $requests);

        /** @var RequestInterface|MessageInterface $request */
        $request = reset($requests);

        $this->assertEquals(
            'POST',
            $request->getMethod()
        );

        $this->assertEquals(
            'http://foo.bar/test/uitpas/checkincode/pdf/' . $id,
            $request->getUrl()
        );
    }

    /**
     * @test
     */
    public function it_can_return_a_pdf_file_download_stream()
    {
        $id = new StringLiteral('132456');
        $contentDispositionHeader = 'attachment; filename="UITPAS_QR_Ancienne_Belgique.pdf"';

        $responseBody = EntityBody::factory(
            fopen(__DIR__ . '/data/UITPAS_QR_Ancienne_Belgique.pdf', 'r')
        );

        $mockResponse = (new Response(200))
            ->setBody($responseBody)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', $contentDispositionHeader);

        $this->mockPlugin->addResponse($mockResponse);

        $expected = new CheckInCodeDownload(
            $responseBody,
            new StringLiteral('application/pdf'),
            new ContentDispositionHeader($contentDispositionHeader)
        );

        $actual = $this->service->download($id, false);

        $this->assertEquals($expected, $actual);

        $requests = $this->mockPlugin->getReceivedRequests();
        $this->assertCount(1, $requests);

        /** @var RequestInterface|MessageInterface $request */
        $request = reset($requests);

        $this->assertEquals(
            'POST',
            $request->getMethod()
        );

        $this->assertEquals(
            'http://foo.bar/test/uitpas/checkincode/pdf/' . $id,
            $request->getUrl()
        );
    }

    /**
     * @test
     */
    public function it_converts_no_future_checkin_periods_errors()
    {
        $id = new StringLiteral('132456');

        $mockResponse = (new Response(400))
            ->setBody(
                file_get_contents(__DIR__ . '/data/no_future_checkin_periods.xml')
            )
            ->setHeader('Content-Type', 'application/xml');

        $this->mockPlugin->addResponse($mockResponse);

        $this->setExpectedException(NoFurtherCheckInPeriodsException::class);

        $this->service->download($id, false);
    }

    /**
     * @test
     */
    public function it_converts_unknown_event_cdbid_errors()
    {
        $id = new StringLiteral('132456');

        $mockResponse = (new Response(400))
            ->setBody(
                file_get_contents(__DIR__ . '/data/unknown_event_cdbid.xml')
            )
            ->setHeader('Content-Type', 'application/xml');

        $this->mockPlugin->addResponse($mockResponse);

        $this->setExpectedException(UnknownActivityException::class);

        $this->service->download($id, false);
    }
}
