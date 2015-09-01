<?php

namespace CultuurNet\UiTPASBeheer\Legacy;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

/**
 * Only meant to be used where CultureFeed objects are required in controllers.
 */
interface LegacyPassHolderServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @return \CultureFeed_Uitpas_PassHolder|null
     */
    public function getByUiTPASNumber(UiTPASNumber $uitpasNumber);
}
