<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

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

    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByUitpasNumber(UiTPASNumber $uitpasNumber)
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

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     */
    public function update(
        UiTPASNumber $uitpasNumber,
        \CultureFeed_Uitpas_Passholder $passHolder
    ) {
        $passHolder->uitpasNumber = $uitpasNumber->toNative();

        $this
            ->getUitpasService()
            ->updatePassholder(
                $passHolder,
                $this->getCounterConsumerKey()
            );
    }
}
