<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\DefaultHttpClientFactory;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use Guzzle\Http\EntityBody;
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
            'http://foo.bar/',
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
     * @dataProvider contentDispositionHeaderDataProvider
     *
     * @param string $originalContentDispositionHeader
     * @param string $expectedContentDispositionHeader
     */
    public function it_can_return_a_file_download_stream(
        $originalContentDispositionHeader,
        $expectedContentDispositionHeader
    ) {
        $id = new ExpenseReportId('132456');

        $responseBody = EntityBody::factory(
            fopen(__DIR__ . '/data/financialOverview_CC De Werf _20151007_1404.zip', 'r')
        );

        $mockResponse = (new Response(200))
            ->setBody($responseBody)
            ->setHeader('Content-Type', 'application/x-zip-compressed')
            ->setHeader('Content-Disposition', (string) $originalContentDispositionHeader);

        $this->mockPlugin->addResponse($mockResponse);

        $expected = new ExpenseReportDownload(
            $responseBody,
            new StringLiteral('application/x-zip-compressed'),
            new StringLiteral((string) $expectedContentDispositionHeader)
        );

        $actual = $this->apiService->download($id);

        $this->assertEquals($expected, $actual);
    }

    public function contentDispositionHeaderDataProvider()
    {
        return [
            [
                'attachment; filename=file name with spaces.zip',
                'attachment; filename="file name with spaces.zip"',
            ],
            [
                'attachment; filename="encapsulated file name.zip"',
                'attachment; filename="encapsulated file name.zip"',
            ],
            [
                'render;',
                'render;',
            ]
        ];
    }
}
