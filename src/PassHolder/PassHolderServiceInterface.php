<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

interface PassHolderServiceInterface
{
    /**
     * @param string $identification
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByIdentificationNumber($identification);

    /**
     * @param string $uitpasNumber
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByUitpasNumber($uitpasNumber);

    /**
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     */
    public function update(\CultureFeed_Uitpas_Passholder $passHolder);
}
