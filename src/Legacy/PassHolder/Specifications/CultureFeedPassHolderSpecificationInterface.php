<?php

namespace CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications;

interface CultureFeedPassHolderSpecificationInterface
{
    public static function isSatisfiedBy(\CultureFeed_Uitpas_Passholder $cfPassHolder);
}
