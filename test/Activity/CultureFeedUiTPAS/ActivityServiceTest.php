<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultureFeed_ResultSet;
use CultureFeed_Uitpas_Event_CultureEvent;
use CultuurNet\Search\SearchResult;
use CultuurNet\Search\ServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
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
     * @var \CultuurNet\Search\ServiceInterface|\PHPUnit_Framework_MockObject_MockObject
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
        $this->searchService = $this->getMock(ServiceInterface::class);

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

        $searchEventOptions = new \CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $searchEventOptions->q = 'foo';

        $searchEventOptionsForCounter = clone $searchEventOptions;
        $searchEventOptionsForCounter->balieConsumerKey = $this->counterConsumerKey->toNative();

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->with($searchEventOptionsForCounter)
            ->willReturn($result_set);

        $query = $this->getMock(SearchOptionsBuilderInterface::class);
        $query->expects($this->once())
            ->method('build')
            ->willReturn($searchEventOptions);

        $emptySearchResult = new SearchResult();
        $this->searchService->expects($this->atLeastOnce())
            ->method('search')
            ->willReturn($emptySearchResult);

        $actual = $this->service->search($query);

        $expected = array(
            new Activity(
                new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
                new StringLiteral('test event 1'),
                new StringLiteral(''),
                new StringLiteral('')
            ),
            new Activity(
                new StringLiteral('ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj'),
                new StringLiteral('test event 2'),
                new StringLiteral(''),
                new StringLiteral('')
            ),
        );

        $this->assertEquals($expected, $actual);
    }
}
