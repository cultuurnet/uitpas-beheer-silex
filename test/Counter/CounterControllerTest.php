<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\Request;

class CounterControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterController
     */
    protected $controller;

    /**
     * @var CounterServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    public function setUp()
    {
        $this->service = $this->getMock(CounterServiceInterface::class);
        $this->controller = new CounterController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_the_users_counters()
    {
        /* @var \CultureFeed_Uitpas_Counter_Employee[] $counters */
        $counters = array();
        for ($i = 1; $i <= 5; $i++) {
            $counter = new \CultureFeed_Uitpas_Counter_Employee();
            $counter->id = $i;

            $counters[$i] = $counter;
        }

        $this->service->expects($this->once())
            ->method('getCounters')
            ->willReturn($counters);

        $response = $this->controller->getCounters();
        $content = $response->getContent();

        $expected = json_encode($counters);

        $this->assertEquals($expected, $content);
    }

    /**
     * @test
     */
    public function it_sets_the_active_counter_id_and_responds_the_counters_data()
    {
        $counter = new \CultureFeed_Uitpas_Counter_Employee();
        $counter->id = 10;

        $this->service->expects($this->once())
            ->method('setActiveCounterId')
            ->with($counter->id);

        $this->service->expects($this->once())
            ->method('getActiveCounter')
            ->willReturn($counter);

        $request = new Request([], ['id' => $counter->id]);

        $response = $this->controller->setActiveCounter($request);
        $content = $response->getContent();

        $expected = json_encode($counter);

        $this->assertEquals($expected, $content);
    }

    /**
     * @test
     */
    public function it_responds_the_active_counters_data()
    {
        $counter = new \CultureFeed_Uitpas_Counter_Employee();
        $counter->id = 10;

        $this->service->expects($this->once())
            ->method('getActiveCounter')
            ->willReturn($counter);

        $response = $this->controller->getActiveCounter();
        $content = $response->getContent();

        $expected = json_encode($counter);

        $this->assertEquals($expected, $content);
    }
}
