<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;

class HasAnyOfNumbers implements UiTPASSpecificationInterface
{
    /**
     * @var UiTPASNumberCollection
     */
    private $uitpasNumbers;

    /**
     * @param UiTPASNumberCollection $uitpasNumbers
     */
    public function __construct(UiTPASNumberCollection $uitpasNumbers)
    {
        $this->uitpasNumbers = $uitpasNumbers;
    }

    /**
     * @param UiTPAS $uitpas
     * @return bool
     */
    public function isSatisfiedBy(UiTPAS $uitpas)
    {
        /* @var UiTPASNumber $uitpasNumber */
        foreach ($this->uitpasNumbers as $uitpasNumber) {
            if ($uitpas->getNumber()->sameValueAs($uitpasNumber)) {
                return true;
            }
        }

        return false;
    }
}
