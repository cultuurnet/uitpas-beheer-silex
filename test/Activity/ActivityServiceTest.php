<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Event_CultureEvent;
use CultureFeed_ResultSet;
use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Search\Guzzle\Service;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityServiceTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var ActivityService
     */
    protected $service;

    /**
     * @var \CultuurNet\Search\Guzzle\Service
     */
    protected $searchService;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    public function setUp()
    {

        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');
        $arguments = array(' http://acc.uitid.be/uitid/rest/searchv2/', $this->getMock(ConsumerCredentials::class));
        $this->searchService = $this->getMock(Service::class, null, $arguments);

        $this->service = new ActivityService($this->uitpas, $this->counterConsumerKey, $this->searchService);

    }

    /**
     * @test
     */
    public function it_can_search_for_activities()
    {
        $event_a = new CultureFeed_Uitpas_Event_CultureEvent();
        $event_a->cdbid = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        $event_a->title = 'test event 1';
        $event_b = new CultureFeed_Uitpas_Event_CultureEvent();
        $event_b->cdbid = 'ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj';
        $event_b->title = 'test event 2';

        $result_set = new \CultureFeed_ResultSet();
        $result_set->total = 2;
        $result_set->objects = array(
            $event_a,
            $event_b,
        );

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->willReturn($result_set);

        $date = DateType::fromNative('today');
        $limit = new Integer(10);
        $query = null;
        $page = null;

        $actual = $this->service->search($date, $limit, $query, $page);

        $expected = array(
            new Activity(
                new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
                new StringLiteral('test event 1'),
                new StringLiteral('test event 1 description'),
                new StringLiteral('test event 1 date')
            ),
            new Activity(
                new StringLiteral('ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj'),
                new StringLiteral('test event 2'),
                new StringLiteral('test event 2 description'),
                new StringLiteral('test event 2 date')
            ),
        );

        $this->assertEquals($expected, $actual);
    }
}
