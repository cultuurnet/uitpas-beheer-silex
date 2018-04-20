<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\DateTime\Hour;
use ValueObjects\DateTime\Minute;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Second;
use ValueObjects\DateTime\Time;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var ActivityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var QueryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $query;

    /**
     * @var ActivityController
     */
    protected $controller;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        date_default_timezone_set('UTC');

        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);
        $this->service = $this->getMock(ActivityServiceInterface::class);
        $this->query = new SimpleQuery();
        $this->controller = new ActivityController(
            $this->service,
            $this->query,
            $this->urlGenerator
        );
    }

    /**
     * @return array
     */
    public function requestsAndCorrespondingQuery()
    {
        $items['no request parameters, only uitpas number'] = [
            '0930000467512',
            [],
            (new SimpleQuery())
                ->withPagination(new Integer(1), new Integer(5))
                ->withUiTPASNumber(new UiTPASNumber('0930000467512'))
                ->withDateRange(new Integer(0), new Integer(0)),
        ];

        $items['all possible parameters'] = [
            '0930000467512',
            [
                'date_type' => 'today',
                'limit' => 10,
                'query' => 'foo',
                'sort' => 'permanent desc,availableto asc',
                'page' => 2,
            ],
            (new SimpleQuery())
                ->withDateType(DateType::TODAY())
                ->withPagination(new Integer(2), new Integer(10))
                ->withQuery(new StringLiteral('foo'))
                ->withUiTPASNumber(new UiTPASNumber('0930000467512'))
                ->withDateRange(new Integer(0), new Integer(0))
                ->withSort('permanent desc,availableto asc'),
        ];

        $items['next 12 months'] = [
            '0930000208908',
            [
                'date_type' => 'next_12_months',
                'limit' => 20,
                'query' => 'bar',
                'sort' => 'permanent desc,availableto asc',
                'page' => 3,
            ],
            (new SimpleQuery())
                ->withDateType(DateType::NEXT_12_MONTHS())
                ->withPagination(new Integer(3), new Integer(20))
                ->withQuery(new StringLiteral('bar'))
                ->withUiTPASNumber(new UiTPASNumber('0930000208908'))
                ->withDateRange(new Integer(0), new Integer(0))
                ->withSort('permanent desc,availableto asc'),

        ];

        $items['without uitpas number'] = [
            null,
            [
                'date_type' => 'next_12_months',
            ],
            (new SimpleQuery())
                ->withDateType(DateType::NEXT_12_MONTHS())
                ->withPagination(
                    new Integer(1),
                    new Integer(5)
                )
                ->withDateRange(new Integer(0), new Integer(0)),
        ];

        return $items;
    }

    /**
     * @test
     *
     * @param string|null $uitpasNumber
     * @param array $request
     * @param SimpleQuery $expectedQuery
     *
     * @dataProvider requestsAndCorrespondingQuery
     */
    public function it_builds_a_query_from_request_parameters_and_passes_it_to_search(
        $uitpasNumber,
        array $request,
        SimpleQuery $expectedQuery
    ) {
        $request = new Request($request);

        $this->service->expects($this->once())
            ->method('search')
            ->with($this->equalTo($expectedQuery))
            ->willReturn(new PagedResultSet(new Integer(0), []));

        $this->controller->search($request, $uitpasNumber);
    }

    /**
     * @test
     */
    public function it_responds_with_the_json_encoded_activities_returned_by_search()
    {
        $uitpasNumber = '0930000208908';

        $activities = [];

        $checkinStartDate = new DateTime(
            new Date(
                Year::fromNative(2015),
                Month::getByName('SEPTEMBER'),
                MonthDay::fromNative(1)
            ),
            new Time(
                Hour::fromNative(9),
                Minute::fromNative(0),
                Second::fromNative(0)
            )
        );

        $checkinEndDate = new DateTime(
            new Date(
                Year::fromNative(2016),
                Month::getByName('MARCH'),
                MonthDay::fromNative(1)
            ),
            new Time(
                Hour::fromNative(16),
                Minute::fromNative(0),
                Second::fromNative(0)
            )
        );

        $checkinConstraint = new CheckinConstraint(
            false,
            $checkinStartDate,
            $checkinEndDate
        );
        $checkinConstraint = $checkinConstraint->withReason(new StringLiteral('INVALID_DATE_TIME'));

        $activity1 = new Activity(
            new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
            new StringLiteral('test event 1'),
            new StringLiteral('test event 1 description'),
            $checkinConstraint,
            new Integer(1)
        );

        $activity1->setWhen(
            new StringLiteral('test event 1 date')
        );

        $activities[] = $activity1;

        $activity2 = new Activity(
            new StringLiteral('ffffffff-gggg-hhhh-iiii-jjjjjjjjjjjj'),
            new StringLiteral('test event 2'),
            new StringLiteral('test event 2 description'),
            $checkinConstraint,
            new Integer(1)
        );

        $activity2->setWhen(
            new StringLiteral('test event 2 date')
        );

        $activities[] = $activity2;

        $this->service
            ->expects($this->once())
            ->method('search')
            ->willReturn(
                new PagedResultSet(
                    // The default limit is 5, so to have 3 pages of results we
                    // need a total of at least 11 results.
                    new Integer(15),
                    $activities
                )
            );

        $routeName = 'route-used-for-activities';
        $this->urlGenerator->expects($this->exactly(3))
            ->method('generate')
            ->withConsecutive(
                [$routeName, ['page' => 1, 'date_type' => 'today', 'uitpasNumber' => $uitpasNumber]],
                [$routeName, ['page' => 3, 'date_type' => 'today', 'uitpasNumber' => $uitpasNumber]],
                [$routeName, ['page' => 2, 'date_type' => 'today', 'uitpasNumber' => $uitpasNumber]]
            )
            ->willReturnCallback(
                function ($routeName, $arguments) {
                    // uitpasNumber parameter will used in the path, not the query.
                    $uitpas = $arguments['uitpasNumber'];
                    unset($arguments['uitpasNumber']);

                    return 'http://example.com/passholders/' . $uitpas . '/activities?' . http_build_query(
                        $arguments
                    );
                }
            );

        $request = new Request();
        $request->attributes->set('_route', $routeName);
        $request->query->set('date_type', 'today');

        $response = $this->controller->search($request, $uitpasNumber);
        $content = $response->getContent();

        $this->assertJsonEquals($content, 'Activity/data/activities.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_on_an_unknown_request_parameter()
    {
        $request = new Request(['foo' => 'bar']);
        $this->setExpectedException(
            UnknownParameterException::class,
            'Unknown parameter "foo"'
        );
        $this->controller->search($request, '0930000208908');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_date_type_parameter_is_invalid()
    {
        $request = new Request(['date_type' => 'yesterday']);
        $this->setExpectedException(
            DateTypeInvalidException::class,
            'Invalid date type "yesterday".'
        );
        $this->controller->search($request, '0930000208908');
    }
}
