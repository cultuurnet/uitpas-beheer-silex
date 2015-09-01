<?php

namespace CultuurNet\UiTPASBeheer\Membership\Specifications;

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
