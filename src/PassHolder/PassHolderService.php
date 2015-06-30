<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;

class PassHolderService extends CounterAwareUitpasService implements PassHolderServiceInterface
{
    /**
     * @param $identification
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByIdentificationNumber($identification)
    {
        try {
            return $this
                    ->getUitpasService()
                    ->getPassholderByIdentificationNumber(
                        $identification,
                        $this->getCounterConsumerKey()
                    );
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }
}
