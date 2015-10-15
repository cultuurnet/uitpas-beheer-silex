<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Filter;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASCollection;

interface UiTPASFilterInterface
{
    /**
     * @param UiTPASCollection $uitpasCollection
     * @return UiTPASCollection
     */
    public function filter(UiTPASCollection $uitpasCollection);
}
