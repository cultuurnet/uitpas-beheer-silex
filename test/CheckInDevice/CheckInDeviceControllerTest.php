<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class CheckInDeviceControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckInDeviceServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkInDevices;

    /**
     * @var CheckInDeviceController
     */
    private $controller;

    public function setUp()
    {
        $this->checkInDevices = $this->getMock(
            CheckInDeviceServiceInterface::class
        );

        $this->controller = new CheckInDeviceController(
            $this->checkInDevices
        );
    }

    /**
     * @test
     */
    public function it_shows_basic_activity_info()
    {
        $activity1 = new Activity(
            new StringLiteral('123'),
            new StringLiteral('Activity 123'),
            new CheckinConstraint(
                true,
                DateTime::now(),
                DateTime::now()
            ),
            new Integer(5)
        );

        $activity2 = new Activity(
            new StringLiteral('456'),
            new StringLiteral('Activity 456'),
            new CheckinConstraint(
                true,
                DateTime::now(),
                DateTime::now()
            ),
            new Integer(3)
        );

        $this->checkInDevices->expects($this->once())
            ->method('availableActivities')
            ->willReturn(
                [
                    $activity1,
                    $activity2,
                ]
            );

        $response = $this->controller->availableActivities();
        $json = $response->getContent();

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/activities.json',
            $json
        );
    }

    /**
     * @test
     */
    public function it_lists_all_check_in_devices()
    {
        $allDevices = [
            new CheckInDevice(
                new StringLiteral('xyz'),
                new StringLiteral('cid test 1')
            ),
            (new CheckInDevice(
                new StringLiteral('abc'),
                new StringLiteral('cid test 2')
            ))->withActivity(new StringLiteral('123-abc')),
        ];

        $this->checkInDevices->expects($this->once())
            ->method('all')
            ->wilLReturn($allDevices);

        $response = $this->controller->all();
        $json = $response->getContent();

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/devices.json',
            $json
        );
    }

    /**
     * @test
     */
    public function it_can_connect_a_device_to_an_activity()
    {
        $deviceId = new StringLiteral('foo');
        $activityId = new StringLiteral('123-456-789');

        $activity = (new CheckInDevice(
            $deviceId,
            new StringLiteral('test device')
        ))->withActivity($activityId);

        $this->checkInDevices->expects($this->once())
            ->method('connectDeviceToActivity')
            ->with(
                $deviceId,
                $activityId
            )
            ->willReturn(
                $activity
            );

        $requestBody = '{"activityId": "123-456-789"}';
        $request = new Request([], [], [], [], [], [], $requestBody);

        $response = $this->controller->connectDeviceToActivity($request, 'foo');
        $json = $response->getContent();

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/device.json',
            $json
        );
    }
}
