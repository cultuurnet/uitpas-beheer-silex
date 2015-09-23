<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;


use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use ValueObjects\DateTime\Date;
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
}
