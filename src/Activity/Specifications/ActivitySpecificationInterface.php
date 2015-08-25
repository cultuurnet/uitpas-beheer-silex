<?php

namespace CultuurNet\UiTPASBeheer\Activity\Specifications;

use CultuurNet\UiTPASBeheer\Activity\Activity;

interface ActivitySpecificationInterface
{
    /**
     * @param Activity $activity
     * @return bool
     */
    public static function isSatisfiedBy(Activity $activity);
}
