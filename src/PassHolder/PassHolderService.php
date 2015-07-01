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

    /**
     * @param string $uitpasNumber
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByUitpasNumber($uitpasNumber)
    {
        try {
            return $this
                    ->getUitpasService()
                    ->getPassholderByUitpasNumber(
                        $uitpasNumber,
                        $this->getCounterConsumerKey()
                    );
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     */
    public function update(\CultureFeed_Uitpas_Passholder $passHolder)
    {
        $this
            ->getUitpasService()
            ->updatePassholder(
                $passHolder,
                $this->getCounterConsumerKey()
            );
    }
}
