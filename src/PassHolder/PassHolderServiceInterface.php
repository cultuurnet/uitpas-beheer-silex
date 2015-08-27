<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
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
     * @param PassHolder $passHolder
     * @param VoucherNumber $voucherNumber
     * @param KansenStatuut $kansenStatuut
     *
     * @return UUID
     *  The UUID assigned to the new passholder.
     **/
    public function register(
        UiTPASNumber $uitpasNumber,
        PassHolder $passHolder,
        VoucherNumber $voucherNumber = null,
        KansenStatuut $kansenStatuut = null
    );
}
