<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Event_CultureEvent;
use CultureFeed_Uitpas_Event_Query_SearchEventsOptions;
use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ValueObjects\StringLiteral\StringLiteral;

class CheckInDeviceService extends CounterAwareUitpasService implements CheckInDeviceServiceInterface
{
    /**
     * @var Clock
     */
    private $clock;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param Clock $clock
     */
    public function __construct(
        CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        Clock $clock
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);

        $this->clock = $clock;
    }

    /**
     * @return CheckInDevice[]
     */
    public function all()
    {
        $cfDevices = $this->getUitpasService()->getDevices($this->getCounterConsumerKey());

        $devices = array_map(
            function (\CultureFeed_Uitpas_Counter_Device $cfDevice) {
                return CheckInDevice::createFromCultureFeedCounterDevice($cfDevice);
            },
            $cfDevices
        );

        return $devices;
    }

    /**
     * @param DateTimeInterface $time
     * @return DateTimeInterface
     */
    private function in3Days(DateTimeInterface $time)
    {
        $time = DateTimeImmutable::createFromFormat(
            DateTime::W3C,
            $time->format(DateTime::W3C)
        );

        return $time->add(DateInterval::createFromDateString('10 days'));
    }

    /**
     * @inheritdoc
     */
    public function availableActivities()
    {
        $now = $this->clock->getDateTime();
        $in3Days = $this->in3Days($now);

        $searchOptions = new CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $searchOptions->balieConsumerKey = $this->getCounterConsumerKey();
        $searchOptions->startDate = $now->getTimestamp();
        $searchOptions->endDate = $in3Days->getTimestamp();
        $searchOptions->sort = 'permanent desc,availableto asc';

        $result = $this->getUitpasService()->searchEvents($searchOptions);

        $activities = array_map(
            function (CultureFeed_Uitpas_Event_CultureEvent $event) {
                return Activity::fromCultureFeedUitpasEvent($event);
            },
            $result->objects
        );

        return $activities;
    }

    /**
     * @param StringLiteral $checkInDeviceId
     * @param StringLiteral $activityId
     * @return CheckInDevice
     */
    public function connectDeviceToActivity(
        StringLiteral $checkInDeviceId,
        StringLiteral $activityId
    ) {
        $cfDevice = $this->getUitpasService()->connectDeviceWithEvent(
            $checkInDeviceId->toNative(),
            $activityId->toNative(),
            $this->getCounterConsumerKey()
        );

        return CheckInDevice::createFromCultureFeedCounterDevice($cfDevice);
    }
}
