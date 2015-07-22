<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\SearchAPI2;

use CultuurNet\CalendarSummary\CalendarFormatterInterface;
use CultuurNet\CalendarSummary\FormatterException;
use CultuurNet\Search\Parameter\BooleanParameter;
use CultuurNet\Search\Parameter\Group;
use CultuurNet\Search\Parameter\Query;
use CultuurNet\Search\SearchResult;
use CultuurNet\Search\ServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Activity\SimpleQuery;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class SearchAPI2AugmentedActivityServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchService;

    /**
     * @var ActivityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $decoratedActivityService;

    /**
     * @var SearchAPI2AugmentedActivityService
     */
    protected $activityService;

    /**
     * @var CalendarFormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calendarFormatter;

    public function setUp()
    {
        $this->searchService = $this->getMock(ServiceInterface::class);
        $this->decoratedActivityService = $this->getMock(
            ActivityServiceInterface::class
        );
        $this->calendarFormatter = $this->getMock(
            CalendarFormatterInterface::class
        );

        $this->activityService = new SearchAPI2AugmentedActivityService(
            $this->decoratedActivityService,
            $this->searchService,
            $this->calendarFormatter
        );
    }

    /**
     * @param SimpleQuery $query
     * @return PagedResultSet
     *   A paged result set with 3 activities on the current page.
     */
    private function setUpDecoratedActivityService(SimpleQuery $query)
    {
        /** @var Activity[] $activities */
        $activities = [
            new Activity(
                new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
                new StringLiteral('test event 1')
            ),
            new Activity(
                new StringLiteral('ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj'),
                new StringLiteral('test event 2')
            ),
            new Activity(
                new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-jjjjjjjjjjjj'),
                new StringLiteral('test event 3')
            ),
        ];

        $resultSet = new PagedResultSet(
            new Integer(10),
            $activities
        );

        $this->decoratedActivityService->expects($this->once())
            ->method('search')
            ->with($this->equalTo($query))
            ->willReturn($resultSet);

        return $resultSet;
    }

    /**
     * @test
     */
    public function it_searches_for_each_activity_found_by_the_decorated_service()
    {
        $query = (new SimpleQuery())->withQuery(new StringLiteral('foo'));
        $originalResultSet = $this->setUpDecoratedActivityService($query);
        $activities = $originalResultSet->getActivities();

        $emptySearchResult = new SearchResult();
        $this->searchService->expects($this->exactly(3))
            ->method('search')
            ->withConsecutive(
                [$this->expectedSearchParametersForActivityWithId($activities[0]->getId())],
                [$this->expectedSearchParametersForActivityWithId($activities[1]->getId())],
                [$this->expectedSearchParametersForActivityWithId($activities[2]->getId())]
            )
            ->willReturn($emptySearchResult);

        $this->activityService->search($query);
    }

    /**
     * @test
     */
    public function it_returns_the_original_activity_when_search_fails()
    {
        $query = (new SimpleQuery())->withQuery(new StringLiteral('bar'));
        $originalResultSet = $this->setUpDecoratedActivityService($query);

        $emptySearchResult = new SearchResult();
        $this->searchService->expects($this->any())
            ->method('search')
            ->willReturn($emptySearchResult);

        $resultSet = $this->activityService->search($query);

        $this->assertEquals(
            $originalResultSet,
            $resultSet
        );
    }

    /**
     * @test
     */
    public function it_adds_description_and_when_to_an_activity_when_search_succeeds()
    {
        $query = (new SimpleQuery())->withQuery(new StringLiteral('foobar'));
        $originalResultSet = $this->setUpDecoratedActivityService($query);
        $activities = $originalResultSet->getActivities();

        $this->searchService->expects($this->any())
            ->method('search')
            ->willReturnCallback(
                function ($params) {
                    return $this->searchResultForParams($params);
                }
            );

        $this->calendarFormatter->expects($this->exactly(3))
            ->method('format')
            ->willReturnCallback(
                function (\CultureFeed_Cdb_Data_Calendar_TimestampList $calendar) {
                    /** @var \CultureFeed_Cdb_Data_Calendar_Timestamp $timestamp */
                    $calendar->rewind();
                    $timestamp = $calendar->current();

                    return $timestamp->getDate();
                }
            );

        $expectedActivities = [
            $activities[0]
                ->withDescription(new StringLiteral('description test event 1'))
                ->withWhen(new StringLiteral('2016-06-01')),
            $activities[1]
                ->withDescription(new StringLiteral('description test event 2'))
                ->withWhen(new StringLiteral('2016-08-01')),
            $activities[2]
                ->withDescription(new StringLiteral('description test event 3'))
                ->withWhen(new StringLiteral('2016-07-01')),
        ];

        $expectedResultSet = new PagedResultSet(
            $originalResultSet->getTotal(),
            $expectedActivities
        );

        $resultSet = $this->activityService->search($query);

        $this->assertEquals($expectedResultSet, $resultSet);
    }

    /**
     * @test
     */
    public function it_only_adds_description_to_an_activity_when_formatting_the_calendar_fails()
    {
        $query = (new SimpleQuery())->withQuery(new StringLiteral('foobar'));
        $originalResultSet = $this->setUpDecoratedActivityService($query);
        $activities = $originalResultSet->getActivities();

        $this->searchService->expects($this->any())
            ->method('search')
            ->willReturnCallback(
                function ($params) {
                    return $this->searchResultForParams($params);
                }
            );

        $this->calendarFormatter->expects($this->any())
            ->method('format')
            ->willThrowException(new FormatterException());

        $expectedActivities = [
            $activities[0]
                ->withDescription(new StringLiteral('description test event 1')),
            $activities[1]
                ->withDescription(new StringLiteral('description test event 2')),
            $activities[2]
                ->withDescription(new StringLiteral('description test event 3')),
        ];

        $expectedResultSet = new PagedResultSet(
            $originalResultSet->getTotal(),
            $expectedActivities
        );

        $resultSet = $this->activityService->search($query);

        $this->assertEquals($expectedResultSet, $resultSet);
    }

    /**
     * @param array $params
     *
     * @return SearchResult
     */
    private function searchResultForParams(array $params)
    {
        foreach ($params as $param) {
            if ($param instanceof Query) {
                preg_match(
                    '/cdbid:"(?<cdbid>[a-zA-Z-0-9-]+)"/',
                    $param->getValue(),
                    $matches
                );
                $cdbid = $matches['cdbid'];

                $path = __DIR__ . "/../data/search/{$cdbid}.xml";

                return $this->createSearchResultFromFile($path);
            }
        }

        throw new \LogicException('No Query parameter found.');
    }

    /**
     * @param string $path
     *
     * @return SearchResult
     */
    private function createSearchResultFromFile($path, $cdbXmlVersion = '3.2')
    {
        $cdbXmlNamespace = \CultureFeed_Cdb_Xml::namespaceUriForVersion(
            $cdbXmlVersion
        );

        $xmlString = file_get_contents($path);
        $xml = new \SimpleXMLElement(
            $xmlString,
            0,
            false,
            $cdbXmlNamespace
        );

        $searchResult = SearchResult::fromXml(
            $xml,
            $cdbXmlNamespace
        );

        return $searchResult;
    }

    private function expectedSearchParametersForActivityWithId(
        StringLiteral $id
    ) {
        return [
            new BooleanParameter('past', true),
            new BooleanParameter('unavailable', true),
            new Group(),
            new Query('cdbid:"' . $id->toNative() . '"')
        ];
    }
}
