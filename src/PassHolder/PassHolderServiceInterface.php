<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

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
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     */
    public function update(UiTPASNumber $uitpasNumber, \CultureFeed_Uitpas_Passholder $passHolder);
}
