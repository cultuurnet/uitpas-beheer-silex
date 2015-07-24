<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultureFeed_ResultSet;
use CultureFeed_Uitpas_Event_CultureEvent;
use CultuurNet\Search\SearchResult;
use CultuurNet\Search\ServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\DateTime;
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
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new ActivityService(
            $this->uitpas,
            $this->counterConsumerKey
        );
    }

    /**
     * @test
     */
    public function it_can_search_for_activities()
    {
        $event_a = new CultureFeed_Uitpas_Event_CultureEvent();
        $event_a->cdbid = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        $event_a->title = 'test event 1';
        $event_a->checkinAllowed = false;
        $event_a->checkinStartDate = "2015-09-01T09:00:00+02:00";
        $event_a->checkinEndDate = "2016-03-01T16:00:00.000+02:00";
        $event_a->checkinConstraintReason = "INVALID_DATE_TIME";
        $event_b = new CultureFeed_Uitpas_Event_CultureEvent();
        $event_b->cdbid = 'ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj';
        $event_b->title = 'test event 2';
        $event_b->checkinAllowed = false;
        $event_b->checkinStartDate = "2015-09-01T09:00:00+02:00";
        $event_b->checkinEndDate = "2016-03-01T16:00:00.000+02:00";
        $event_b->checkinConstraintReason = "INVALID_DATE_TIME";

        $result_set = new \CultureFeed_ResultSet();
        $result_set->total = 20;
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



        $actual = $this->service->search($query);

        $checkinStartDate = \DateTime::createFromFormat('U', 1441098000);
        $checkinEndDate = \DateTime::createFromFormat('U', 1456848000);
        $checkinConstraint = new CheckinConstraint(
            false,
            DateTime::fromNativeDateTime($checkinStartDate),
            DateTime::fromNativeDateTime($checkinEndDate)
        );
        $checkinConstraint = $checkinConstraint->withReason(new StringLiteral('INVALID_DATE_TIME'));

        $expected = new PagedResultSet(
            new Integer($result_set->total),
            [
                new Activity(
                    new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
                    new StringLiteral('test event 1'),
                    $checkinConstraint
                ),
                new Activity(
                    new StringLiteral('ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj'),
                    new StringLiteral('test event 2'),
                    $checkinConstraint
                ),
            ]
        );

        $this->assertEquals($expected, $actual);
    }
}
