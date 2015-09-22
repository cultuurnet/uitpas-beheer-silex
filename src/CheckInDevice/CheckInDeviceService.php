<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use ValueObjects\StringLiteral\StringLiteral;

class CheckInDeviceService extends CounterAwareUitpasService implements CheckInDeviceServiceInterface
{
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
