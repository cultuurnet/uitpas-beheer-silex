<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use ValueObjects\StringLiteral\StringLiteral;

interface CheckInDeviceServiceInterface
{
    /**
     * @return CheckInDevice[]
     */
    public function all();

    /**
     * @param StringLiteral $checkInDeviceId
     * @param StringLiteral $activityId
     * @return CheckInDevice
     */
    public function connectDeviceToActivity(
        StringLiteral $checkInDeviceId,
        StringLiteral $activityId
    );
}
