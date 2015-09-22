<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultuurNet\Clock\Clock;
use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use DateTimeImmutable;
use DateTime;
use ValueObjects\StringLiteral\StringLiteral;

class CheckInDeviceServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $counterConsumerKey;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var CheckInDeviceService
     */
    protected $service;

    /**
     * @var Clock
     */
    protected $clock;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = 'some-counter-key';
        $this->clock = new FrozenClock(
            DateTimeImmutable::createFromFormat(
                DateTime::ATOM,
                '2015-09-25T10:25:50+02:00'
            )
        );

        $this->service = new CheckInDeviceService(
            $this->uitpas,
            new CounterConsumerKey($this->counterConsumerKey),
            $this->clock
        );
    }

    /**
     * @test
     */
    public function it_retrieves_maximum_20_activities_occurring_today_and_the_following_3_days()
    {
        $activity1 = new \CultureFeed_Uitpas_Event_CultureEvent();
        $activity1->cdbid = '123';
        $activity1->title = 'Activity 123';

        $activity2 = new \CultureFeed_Uitpas_Event_CultureEvent();
        $activity2->cdbid = '456';
        $activity2->title = 'Activity 456';

        $resultSet = new \CultureFeed_ResultSet(
            2,
            [
                $activity1,
                $activity2,
            ]
        );

        $expectedSearchStartDate = '2015-09-25T00:00:00+02:00';
        $expectedSearchEndDate = '2015-09-28T23:59:59+02:00';

        $expectedSearchOptions = new \CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $expectedSearchOptions->balieConsumerKey = $this->counterConsumerKey;
        $expectedSearchOptions->sort = 'permanent desc,availableto asc';
        $expectedSearchOptions->max = 20;
        $expectedSearchOptions->startDate = DateTimeImmutable::createFromFormat(
            DateTime::ATOM,
            $expectedSearchStartDate
        )->getTimestamp();
        $expectedSearchOptions->endDate = DateTimeImmutable::createFromFormat(
            DateTime::ATOM,
            $expectedSearchEndDate
        )->getTimestamp();

        $this->uitpas->expects($this->once())
            ->method('searchEvents')
            ->with($expectedSearchOptions)
            ->willReturn($resultSet);

        $expectedActivities = [
            Activity::fromCultureFeedUitpasEvent($activity1),
            Activity::fromCultureFeedUitpasEvent($activity2),
        ];

        $actualActivities = $this->service->availableActivities();

        $this->assertEquals(
            $expectedActivities,
            $actualActivities
        );
    }

    /**
     * @test
     */
    public function it_can_connect_a_device_to_an_activity()
    {
        $deviceId = 'foo';
        $activityId = 'bar';

        $cfDevice = new \CultureFeed_Uitpas_Counter_Device();
        $cfDevice->consumerKey = $deviceId;
        $cfDevice->name = 'some test device';
        $cfDevice->cdbid = $activityId;

        $expectedDevice = (new CheckInDevice(
            new StringLiteral($deviceId),
            new StringLiteral('some test device')
        ))->withActivity(new StringLiteral($activityId));

        $this->uitpas->expects($this->once())
            ->method('connectDeviceWithEvent')
            ->with(
                $deviceId,
                $activityId,
                $this->counterConsumerKey
            )
            ->willReturn(
                $cfDevice
            );

        $actualDevice = $this->service->connectDeviceToActivity(
            new StringLiteral($deviceId),
            new StringLiteral($activityId)
        );

        $this->assertEquals(
            $expectedDevice,
            $actualDevice
        );
    }

    /**
     * @test
     */
    public function it_retrieves_all_check_in_devices()
    {
        $device1 = new \CultureFeed_Uitpas_Counter_Device();
        $device1->consumerKey = 'xyz';
        $device1->name = 'test device 1';

        $device2 = new \CultureFeed_Uitpas_Counter_Device();
        $device2->consumerKey = 'abc';
        $device2->name = 'test device 2';

        $expectedDevices = [
            CheckInDevice::createFromCultureFeedCounterDevice($device1),
            CheckInDevice::createFromCultureFeedCounterDevice($device2),
        ];

        $showEvent = true;

        $this->uitpas->expects($this->once())
            ->method('getDevices')
            ->with(
                $this->counterConsumerKey,
                $showEvent
            )
            ->willReturn(
                [
                    $device1,
                    $device2,
                ]
            );

        $actualDevices = $this->service->all();

        $this->assertEquals(
            $expectedDevices,
            $actualDevices
        );
    }
}
