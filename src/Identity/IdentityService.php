<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;

class IdentityService extends CounterAwareUitpasService
{
    /**
     * @param $identification
     * @return Identity
     */
    public function get($identification)
    {
        try {
            $cfIdentity = $this
                ->getUitpasService()
                ->identify(
                    $identification,
                    $this->getCounterConsumerKey()
                );
            return Identity::fromCultureFeedIdentity($cfIdentity);
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }
}
