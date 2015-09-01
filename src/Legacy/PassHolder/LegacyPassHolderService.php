<?php

namespace CultuurNet\UiTPASBeheer\Legacy\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

/**
 * Only meant to be used where CultureFeed objects are required in controllers.
 */
class LegacyPassHolderService extends CounterAwareUitpasService implements LegacyPassHolderServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @return \CultureFeed_Uitpas_PassHolder|null
     */
    public function getByUiTPASNumber(UiTPASNumber $uitpasNumber)
    {
        try {
            return $this
                ->getUitpasService()
                ->getPassholderByUitpasNumber(
                    $uitpasNumber->toNative(),
                    $this->getCounterConsumerKey()
                );
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }
}
