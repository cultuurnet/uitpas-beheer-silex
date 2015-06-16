<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CounterServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterService
     */
    protected $counterService;

    /**
     * @var \CultureFeed_Uitpas_Counter_Employee[]
     */
    protected $counters;

    /**
     * @var array
     */
    protected $counterData;

    /**
     * @var \CultureFeed_ResultSet
     */
    protected $resultSet;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var \CultureFeed_User
     */
    protected $user;

    public function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());

        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);

        $this->user = new \CultureFeed_User();
        $this->user->id = 1;

        $this->counterService = new CounterService($this->session, $this->uitpas, $this->user);

        $this->counterData = [
            10 => [
                'id' => '10',
                'name' => 'Counter 1',
                'role' => 'member',
                'consumerKey' => 'abc',
                'actorId' => 'def',
            ],
            20 => [
                'id' => '20',
                'name' => 'Counter 2',
                'role' => 'admin',
                'consumerKey' => 'ghi',
                'actorId' => 'jkl',
            ]
        ];

        $this->counters = array();
        foreach ($this->counterData as $id => $counterData) {
            $counter = new \CultureFeed_Uitpas_Counter_Employee();
            foreach ($counterData as $key => $value) {
                $counter->{$key} = $value;
            }
            $this->counters[$id] = $counter;
        }

        $this->resultSet = new \CultureFeed_ResultSet(
            count($this->counters),
            array_values($this->counters)
        );

        $this->uitpas->expects($this->any())
            ->method('searchCountersForMember')
            ->with($this->user->id)
            ->willReturn($this->resultSet);
    }

    /**
     * @test
     */
    public function it_returns_an_associative_array_of_counters()
    {
        $counters = $this->counterService->getCounters();
        $this->assertEquals($this->counters, $counters);
    }

    /**
     * @test
     */
    public function it_returns_a_specific_counter()
    {
        $id = 10;
        $counter = $this->counterService->getCounter($id);
        $this->assertEquals($this->counters[$id], $counter);

        $non_existent_counter = $this->counterService->getCounter(5);
        $this->assertNull($non_existent_counter);
    }

    /**
     * @test
     */
    public function it_can_retrieve_the_active_counter()
    {
        $this->counterService->setActiveCounterId(10);

        $this->assertEquals(
            $this->counters[10],
            $this->counterService->getActiveCounter()
        );
    }

    /**
     * @test
     */
    public function it_fails_to_retrieve_the_active_counter_when_there_is_none()
    {
        $this->assertNull($this->counterService->getActiveCounterId());

        $this->setExpectedException(CounterNotSetException::class);
        $this->counterService->getActiveCounter();
    }

    /**
     * @test
     */
    public function it_stores_the_active_counter_id()
    {
        $id = 10;
        $this->counterService->setActiveCounterId($id);
        $this->assertEquals($id, $this->counterService->getActiveCounterId());
    }

    /**
     * @test
     */
    public function it_fails_to_make_a_non_existing_counter_active()
    {
        $non_existent_id = 5;
        $this->setExpectedException(CounterNotFoundException::class);
        $this->counterService->setActiveCounterId($non_existent_id);
    }
}
