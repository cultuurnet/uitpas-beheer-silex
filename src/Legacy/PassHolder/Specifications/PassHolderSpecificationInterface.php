<?php

namespace CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications;

interface PassHolderSpecificationInterface
{
    public static function isSatisfiedBy(\CultureFeed_Uitpas_Passholder $cfPassHolder);
}
