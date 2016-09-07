<?php

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use CultureFeed_Uitpas;
use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTimeImmutable;
use DateTimeZone;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\QueryString;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Service\Client;
use Guzzle\Service\ClientInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use ValueObjects\StringLiteral\StringLiteral;

class BalieInsightsServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var BalieInsightsService
     */
    protected $service;

    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleClient;

    public function setUp()
    {

        $this->guzzleClient = $this->getMock(ClientInterface::class);

        $this->service = new BalieInsightsService(
            $this->guzzleClient
        );
    }

    /**
     * @test
     */
    public function it_can_request_card_sales()
    {
        $this->request_test('getCardSales', 'cardsales');
    }

    /**
     * @test
     */
    public function it_can_request_checkins()
    {
        $this->request_test('getCheckins', 'checkins');
    }

    /**
     * @test
     */
    public function it_can_request_exchanges()
    {
        $this->request_test('getExchanges', 'exchanges');
    }

    /**
     * @test
     */
    public function it_can_request_mias()
    {
        $this->request_test('getMias', 'mias');
    }

    /**
     * @test
     */
    public function it_can_handle_exceptions()
    {

        $query = new ParameterBag();
        $response = new Response(404);
        $response->setBody(EntityBody::factory('exception body'));

        $request = $this->getMock(RequestInterface::class);
        $exception = BadResponseException::factory($request, $response);

        $request->expects($this->once())
          ->method('send')
          ->willThrowException($exception);

        $this->guzzleClient->expects($this->once())
          ->method('createRequest')
          ->willReturn($request);

        $actual = $this->service->getCardSales($query);

        $this->assertEquals('exception body', $actual);

    }

    /**
     * General method to test if requests are correctly requested via guzzle.
     */
    private function request_test($method, $uri)
    {

        $query = new ParameterBag(
            [
                'from' => '01/07/2016',
                'to' => '15/07/2016',
            ]
        );
        $httpMethod = 'GET';

        $response = new Response(200);
        $response->setBody(EntityBody::factory('body'));
        $request = $this->getMock(RequestInterface::class);

        $request->expects($this->once())
            ->method('send')
            ->willReturn($response);

        $this->guzzleClient->expects($this->once())
          ->method('createRequest')
          ->with($httpMethod, $uri, [], null, ['query' => $query->all()])
          ->willReturn($request);

        $actual = $this->service->{$method}($query);

        $this->assertEquals('body', $actual);

    }
}
