<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\DefaultHttpClientFactory;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\MessageInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use ValueObjects\StringLiteral\StringLiteral;

class ExpenseReportApiServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var ExpenseReportApiService
     */
    protected $apiService;

    /**
     * @var MockPlugin
     */
    protected $mockPlugin;

    public function setUp()
    {
        $this->counterConsumerKey = new CounterConsumerKey('123546578');

        $this->apiService = new ExpenseReportApiService(
            $this->counterConsumerKey,
            'http://foo.bar/test/',
            new ConsumerCredentials(
                'key',
                'secret'
            )
        );

        $this->mockPlugin = new MockPlugin();

        $clientFactory = new DefaultHttpClientFactory();
        $clientFactory->addSubscriber($this->mockPlugin);

        $this->apiService->setHttpClientFactory($clientFactory);
    }

    /**
     * @test
     */
    public function it_can_return_a_file_download_stream()
    {
        $id = new ExpenseReportId('132456');
        $contentDispositionHeader = 'attachment; filename="encapsulated file name.zip"';

        $responseBody = EntityBody::factory(
            fopen(__DIR__ . '/data/financialOverview_CC De Werf _20151007_1404.zip', 'r')
        );

        $mockResponse = (new Response(200))
            ->setBody($responseBody)
            ->setHeader('Content-Type', 'application/x-zip-compressed')
            ->setHeader('Content-Disposition', (string) $contentDispositionHeader);

        $this->mockPlugin->addResponse($mockResponse);

        $expected = new ExpenseReportDownload(
            $responseBody,
            new StringLiteral('application/x-zip-compressed'),
            new StringLiteral((string) $contentDispositionHeader)
        );

        $actual = $this->apiService->download($id);

        $this->assertEquals($expected, $actual);

        $requests = $this->mockPlugin->getReceivedRequests();
        $this->assertCount(1, $requests);

        /** @var RequestInterface|MessageInterface $request */
        $request = reset($requests);

        $this->assertEquals(
            'GET',
            $request->getMethod()
        );

        $expectedUrl = 'http://foo.bar/test/uitpas/report/financialoverview/organiser/' . $id . '/download' .
            '?balieConsumerKey=' . $this->counterConsumerKey;

        $this->assertEquals(
            $expectedUrl,
            $request->getUrl()
        );
    }
}
