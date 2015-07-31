<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class PassHolderService extends CounterAwareUitpasService implements PassHolderServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return PassHolder|null
     */
    public function getByUitpasNumber(UiTPASNumber $uitpasNumber)
    {
        try {
            $cfPassHolder = $this
                    ->getUitpasService()
                    ->getPassholderByUitpasNumber(
                        $uitpasNumber->toNative(),
                        $this->getCounterConsumerKey()
                    );
            return PassHolder::fromCultureFeedPassHolder($cfPassHolder);
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param PassHolder $passHolder
     */
    public function update(
        UiTPASNumber $uitpasNumber,
        PassHolder $passHolder
    ) {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->uitpasNumber = $uitpasNumber->toNative();

        // @todo Set all properties from the PassHolder object on the CultureFeed_Passholder object.
        // @see PassHolderServiceTest

        $this
            ->getUitpasService()
            ->updatePassholder(
                $cfPassHolder,
                $this->getCounterConsumerKey()
            );
    }
}
