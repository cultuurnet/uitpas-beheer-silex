<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;

interface UiTPASSpecificationInterface
{
    /**
     * @param UiTPAS $uitpas
     * @return bool
     */
    public function isSatisfiedBy(UiTPAS $uitpas);
}
