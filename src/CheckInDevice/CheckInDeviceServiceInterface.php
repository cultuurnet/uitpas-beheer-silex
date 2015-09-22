<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use ValueObjects\StringLiteral\StringLiteral;

interface CheckInDeviceServiceInterface
{
    /**
     * @return CheckInDevice[]
     */
    public function all();

    /**
     * @return Activity[]
     */
    public function availableActivities();

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
