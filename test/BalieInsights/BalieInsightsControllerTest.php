<?php

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use Symfony\Component\HttpFoundation\Request;

class BalieInsightsControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var BalieInsightsServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $balieInsightsService;

    /**
     * @var BalieInsightsController
     */
    protected $controller;


    public function setUp()
    {
        $this->balieInsightsService = $this->getMock(BalieInsightsServiceInterface::class);


        $this->controller = new BalieInsightsController(
            $this->balieInsightsService
        );

    }

    /**
     * @test
     */
    public function it_can_return_card_sales()
    {
        $this->response_test('getCardSales', 'data/checkins.json');
    }

    /**
     * @test
     */
    public function it_can_return_checkins()
    {
        $this->response_test('getCheckins', 'data/checkins.json');
    }

    /**
     * @test
     */
    public function it_can_return_exchanges()
    {
        $this->response_test('getExchanges', 'data/exchanges.json');
    }

    /**
     * @test
     */
    public function it_can_return_mias()
    {
        $this->response_test('getMias', 'data/mias.json');
    }

    /**
     * General method to test the basic responses of controller methods.
     * @param $method
     *   Method to call.
     * @param $jsonFile
     *   Json file used to test.
     */
    private function response_test($method, $jsonFile)
    {

        $request = new Request(
            [
                'from' => '01/07/2016',
                'to' => '15/07/2016',
            ]
        );

        $cardSalesJson = file_get_contents(__DIR__ . '/' . $jsonFile);

        $this->balieInsightsService->expects($this->once())
          ->method($method)
          ->with(
              $request->query
          )
          ->willReturn($cardSalesJson);

        $response = $this->controller->{$method}($request);

        $this->assertEquals(200, $response->getStatusCode());

        $json = $response->getContent();

        $this->assertJsonEquals($json, 'BalieInsights/' . $jsonFile);
    }
}
