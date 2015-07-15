<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS\SimpleQuery;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
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
     * @inheritdoc
     */
    public function setUp()
    {
        $this->service = $this->getMock(ActivityServiceInterface::class);
        $this->query = new SimpleQuery();
        $this->controller = new ActivityController(
            $this->service,
            $this->query
        );
    }

    /**
     *
     */
    public function requestsAndCorrespondingQuery()
    {
        // No request parameters.
        $items[] = [
            [],
            (new SimpleQuery())
                ->withPagination(new Integer(1), new Integer(5))
        ];

        // All possible request parameters.
        $items[] = [
            [
                'date_type' => 'today',
                'limit' => 10,
                'query' => 'foo',
                'page' => 2,
                'uitpas_number' => '0930000467512',
            ],
            (new SimpleQuery())
                ->withDateType(DateType::TODAY())
                ->withPagination(new Integer(2), new Integer(10))
                ->withQuery(new StringLiteral('foo'))
                ->withUiTPASNumber(new UiTPASNumber('0930000467512'))
        ];

        $items[] = [
            [
                'date_type' => 'next_12_months',
                'limit' => 20,
                'query' => 'bar',
                'page' => 3,
                'uitpas_number' => '0930000208908',
            ],
            (new SimpleQuery())
                ->withDateType(DateType::NEXT_12_MONTHS())
                ->withPagination(new Integer(3), new Integer(20))
                ->withQuery(new StringLiteral('bar'))
                ->withUiTPASNumber(new UiTPASNumber('0930000208908'))

        ];

        return $items;
    }

    /**
     * @test
     * @dataProvider requestsAndCorrespondingQuery
     */
    public function it_builds_a_query_from_request_parameters_and_passes_it_to_search(
        array $request,
        SimpleQuery $expectedQuery
    ) {
        $request = new Request($request);

        $this->service->expects($this->once())
            ->method('search')
            ->with($this->equalTo($expectedQuery));

        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_responds_with_the_json_encoded_activities_returned_by_search()
    {
        $activities = [
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
        ];

        $this->service
          ->expects($this->once())
          ->method('search')
          ->willReturn($activities);

        $response = $this->controller->search(new Request());
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
        $this->controller->search($request);
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
        $this->controller->search($request);
    }
}
