<?php

namespace CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications;

class HasAtLeastOneExpiredKansenStatuut implements CultureFeedPassHolderSpecificationInterface
{
    public static function isSatisfiedBy(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        foreach ($cfPassHolder->cardSystemSpecific as $cardSystemSpecific) {
            if ($cardSystemSpecific->kansenStatuutExpired) {
                return true;
            }
        }

        return false;
    }
}
