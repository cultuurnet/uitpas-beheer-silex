<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultureFeed_ResultSet;
use CultureFeed_Uitpas_Event_CultureEvent;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityNotFoundException;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Prices;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Integer;
use ValueObjects\Number\Real;
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

    /**
     * @var CultureFeed_Uitpas_Event_CultureEvent
     */
    protected $eventA;

    /**
     * @var CultureFeed_Uitpas_Event_CultureEvent
     */
    protected $eventB;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new ActivityService(
            $this->uitpas,
            $this->counterConsumerKey
        );

        $eventA = new CultureFeed_Uitpas_Event_CultureEvent();
        $eventA->cdbid = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        $eventA->title = 'test event 1';
        $eventA->checkinAllowed = false;
        $eventA->checkinStartDate = 1441098000;
        $eventA->checkinEndDate = 1456848000;
        $eventA->checkinConstraintReason = "INVALID_DATE_TIME";
        $eventA->price = 0.0;
        $eventA->numberOfPoints = 1;
        $this->eventA = $eventA;

        $eventB = new CultureFeed_Uitpas_Event_CultureEvent();
        $eventB->cdbid = 'ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj';
        $eventB->title = 'test event 2';
        $eventB->checkinAllowed = false;
        $eventB->checkinStartDate = 1441098000;
        $eventB->checkinEndDate = 1456848000;
        $eventB->checkinConstraintReason = "INVALID_DATE_TIME";
        $eventB->price = 0.0;
        $eventB->numberOfPoints = 1;
        $this->eventB = $eventB;
    }

    /**
     * @test
     */
    public function it_can_search_for_activities()
    {
        $eventA = $this->eventA;
        $eventB = $this->eventB;

        $resultSet = new \CultureFeed_ResultSet();
        $resultSet->total = 20;
        $resultSet->objects = array(
            $eventA,
            $eventB,
        );

        $searchEventOptions = new \CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $searchEventOptions->q = 'foo';

        $searchEventOptionsForCounter = clone $searchEventOptions;
        $searchEventOptionsForCounter->balieConsumerKey = $this->counterConsumerKey->toNative();

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->with($searchEventOptionsForCounter)
            ->willReturn($resultSet);

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

        $salesInformation = new SalesInformation(
            (new Prices())
                ->withPricing(
                    new PriceClass('Standaard'),
                    new Real(0.0)
                )
        );

        $expected = new PagedResultSet(
            new Integer($resultSet->total),
            [
                (new Activity(
                    new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
                    new StringLiteral('test event 1'),
                    $checkinConstraint,
                    new Integer(1)
                ))->withSalesInformation($salesInformation),
                (new Activity(
                    new StringLiteral('ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj'),
                    new StringLiteral('test event 2'),
                    $checkinConstraint,
                    new Integer(1)
                ))->withSalesInformation($salesInformation),
            ]
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_get_an_activity_by_id_for_a_given_uitpas()
    {
        $resultSet = new \CultureFeed_ResultSet();
        $resultSet->total = 1;
        $resultSet->objects = array(
            $this->eventA,
        );

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->willReturn($resultSet);

        $uitpasNumber = new UiTPASNumber('0930000420206');
        $eventId = new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

        $checkinStartDate = \DateTime::createFromFormat('U', 1441098000);
        $checkinEndDate = \DateTime::createFromFormat('U', 1456848000);
        $checkinConstraint = new CheckinConstraint(
            false,
            DateTime::fromNativeDateTime($checkinStartDate),
            DateTime::fromNativeDateTime($checkinEndDate)
        );
        $checkinConstraint = $checkinConstraint->withReason(new StringLiteral('INVALID_DATE_TIME'));

        $actualActivity = $this->service->get($uitpasNumber, $eventId);

        $expectedActivity = (new Activity(
            new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
            new StringLiteral('test event 1'),
            $checkinConstraint,
            new Integer(1)
        ))->withSalesInformation(
            new SalesInformation(
                (new Prices())
                    ->withPricing(
                        new PriceClass('Standaard'),
                        new Real(0.0)
                    )
            )
        );

        $this->assertEquals($expectedActivity, $actualActivity);
    }

    /**
     * @test
     */
    public function it_should_not_return_an_activity_if_multiple_matching_events_are_found()
    {
        $resultSet = new \CultureFeed_ResultSet();
        $resultSet->total = 2;
        $resultSet->objects = array(
            $this->eventA,
            $this->eventB,
        );

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->willReturn($resultSet);

        $this->setExpectedException(
            ActivityNotFoundException::class,
            'The activity with cdbid aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee was not found.'
        );

        $uitpasNumber = new UiTPASNumber('0930000420206');
        $eventId = new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');
        $this->service->get($uitpasNumber, $eventId);
    }

    /**
     * @test
     */
    public function it_should_not_return_an_activity_if_no_matching_events_are_found()
    {
        $resultSet = new \CultureFeed_ResultSet();
        $resultSet->total = 0;
        $resultSet->objects = array();

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->willReturn($resultSet);

        $this->setExpectedException(
            ActivityNotFoundException::class,
            'The activity with cdbid aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee was not found.'
        );

        $uitpasNumber = new UiTPASNumber('0930000420206');
        $eventId = new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');
        $this->service->get($uitpasNumber, $eventId);
    }
}
