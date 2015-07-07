<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

interface PassHolderServiceInterface
{
    /**
     * @param string $identification
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByIdentificationNumber($identification);

    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByUitpasNumber(UiTPASNumber $uitpasNumber);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     */
    public function update(UiTPASNumber $uitpasNumber, \CultureFeed_Uitpas_Passholder $passHolder);
}
