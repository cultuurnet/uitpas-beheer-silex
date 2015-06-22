<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

interface PassHolderServiceInterface
{
    /**
     * @param string $identification
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getPassHolderByIdentificationNumber($identification);
}
