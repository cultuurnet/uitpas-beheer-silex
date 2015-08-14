<?php

namespace CultuurNet\UiTPASBeheer\Activity\Specifications;

use CultuurNet\UiTPASBeheer\Activity\Activity;

class IsFree implements ActivitySpecificationInterface
{
    /**
     * @param Activity $activity
     * @return bool
     */
    public static function isSatisfiedBy(Activity $activity)
    {
        $salesInformation = $activity->getSalesInformation();
        if (is_null($salesInformation)) {
            return true;
        }

        $basePrices = $salesInformation->getBasePrices();
        foreach ($basePrices as $basePrice) {
            if ((float) $basePrice->toNative() > 0) {
                return false;
            }
        }

        return true;
    }
}
