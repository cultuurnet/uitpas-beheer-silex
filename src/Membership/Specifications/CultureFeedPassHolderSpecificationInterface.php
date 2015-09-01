<?php

namespace CultuurNet\UiTPASBeheer\Membership\Specifications;

interface CultureFeedPassHolderSpecificationInterface
{
    public static function isSatisfiedBy(\CultureFeed_Uitpas_Passholder $cfPassHolder);
}
