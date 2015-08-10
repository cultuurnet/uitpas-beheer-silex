<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\DateTime;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckinController
     */
    protected $controller;

    /**
     * @var ActivityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $activityService;

    /**
     * @var CheckinServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkinService;

    public function setUp()
    {
        $this->activityService = $this->getMock(ActivityServiceInterface::class);
        $this->checkinService = $this->getMock(CheckinServiceInterface::class);

        $this->controller = new CheckinController(
            $this->checkinService,
            $this->activityService,
            new CheckinCommandDeserializer()
        );
    }

    /**
     * @test
     */
    public function it_should_return_an_updated_activity_when_checking_in_a_passholder()
    {
        $checkinData = '{"eventCdbid": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee"}';
        $request = new Request([], [], [], [], [], [], $checkinData);
        $uitpasNumber = '0930000125607';

        $checkinStartDate = \DateTime::createFromFormat('U', 1441098000);
        $checkinEndDate = \DateTime::createFromFormat('U', 1456848000);
        $checkinConstraint = new CheckinConstraint(
            false,
            DateTime::fromNativeDateTime($checkinStartDate),
            DateTime::fromNativeDateTime($checkinEndDate)
        );
        $checkinConstraint = $checkinConstraint->withReason(new StringLiteral('INVALID_DATE_TIME'));

        $updatedActivity = new Activity(
            new StringLiteral('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'),
            new StringLiteral('test event 1'),
            $checkinConstraint
        );

        $this->activityService->expects($this->once())
            ->method('get')
            ->with(new UiTPASNumber($uitpasNumber), new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'))
            ->willReturn($updatedActivity);

        $updatedActivityResponse = $this->controller->checkin($request, $uitpasNumber);

        $expectedResponse = JsonResponse::create()
            ->setData($updatedActivity)
            ->setPrivate();

        $this->assertEquals($expectedResponse, $updatedActivityResponse);
    }

    /**
     * @test
     */
    public function it_should_return_a_readable_error_when_unable_to_find_an_activity()
    {
        $checkinData = '{"eventCdbid": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee"}';
        $request = new Request([], [], [], [], [], [], $checkinData);
        $uitpasNumber = '0930000125607';

        $this->activityService->expects($this->once())
            ->method('get')
            ->with(new UiTPASNumber($uitpasNumber), new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'))
            ->willReturn(null);

        $this->setExpectedException(ActivityNotFoundException::class);

        $this->controller->checkin($request, $uitpasNumber);
    }

    /**
     * @test
     */
    public function it_should_return_a_readable_error_when_unable_to_checkin_a_passholder()
    {
        $checkinData = '{"eventCdbid": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee"}';
        $request = new Request([], [], [], [], [], [], $checkinData);
        $uitpasNumber = '0930000125607';

        $exception = new \CultureFeed_Exception('error', 0);

        $this->checkinService->expects($this->once())
            ->method('checkin')
            ->with(new UiTPASNumber($uitpasNumber), new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'))
            ->willThrowException($exception);

        $this->setExpectedException(ReadableCodeResponseException::class);

        $this->controller->checkin($request, $uitpasNumber);
    }
}
