<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\MissingParameterException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
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
     * @var ActivityController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(ActivityServiceInterface::class);
        $this->controller = new ActivityController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_with_a_list_of_activities_on_search()
    {
        $activities = array(
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

        $dateType = DateType::fromNative('today');
        $limit = new Integer(10);
        $query = new StringLiteral('foo');
        $page = new Integer(2);

        $this->service
          ->expects($this->once())
          ->method('search')
          ->with($dateType, $limit, $query, $page)
          ->willReturn($activities);

        $request = new Request([
            'date_type' => 'today',
            'limit' => 10,
            'query' => 'foo',
            'page' => 2,
        ]);
        $response = $this->controller->search($request);
        $content = $response->getContent();

        $this->assertJsonEquals($content, 'Activity/data/activities.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_date_type_parameter_is_missing()
    {
        $request = new Request(['limit' => 10]);
        $this->setExpectedException(
            MissingParameterException::class,
            'Missing parameter "date_type".'
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

    /**
     * @test
     */
    public function it_throws_an_exception_when_limit_parameter_is_missing()
    {
        $request = new Request(['date_type' => 'today']);
        $this->setExpectedException(
            MissingParameterException::class,
            'Missing parameter "limit".'
        );
        $this->controller->search($request);
    }
}
