<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\Kansenstatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASOffer;
use ValueObjects\Identity\UUID;

interface PassHolderServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return PassHolder|null
     */
    public function getByUitpasNumber(UiTPASNumber $uitpasNumber);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param PassHolder $passHolder
     */
    public function update(UiTPASNumber $uitpasNumber, PassHolder $passHolder);

    /**
     * Register a new passholder to an existing UiTPAS-number.
     *
     * @param UiTPASNumber $uitpasNumber
     * @param Passholder $passholder
     * @param VoucherNumber $voucherNumber
     * @param Kansenstatuut $kansenstatuut
     *
     * @return UUID
     *  The UUID assigned to the new passholder.
     **/
    public function register(
        UiTPASNumber $uitpasNumber,
        Passholder $passholder,
        VoucherNumber $voucherNumber = null,
        Kansenstatuut $kansenstatuut = null
    );

    /**
     * Get a list of all the passholder registration offers.
     * @return UiTPASOffer[]
     */
    public function getOffers();
}
