<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Filter;

use CultuurNet\UiTPASBeheer\UiTPAS\Specifications\UiTPASSpecificationInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASCollection;

class UiTPASSpecificationFilter implements UiTPASFilterInterface
{
    /**
     * @var UiTPASSpecificationInterface
     */
    private $specification;

    /**
     * @param UiTPASSpecificationInterface $specification
     */
    public function __construct(UiTPASSpecificationInterface $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @param UiTPASCollection $uitpasCollection
     * @return UiTPASCollection
     */
    public function filter(UiTPASCollection $uitpasCollection)
    {
        /* @var UiTPAS $uitpas */
        foreach ($uitpasCollection as $uitpas) {
            if (!$this->specification->isSatisfiedBy($uitpas)) {
                $uitpasCollection = $uitpasCollection->without($uitpas);
            }
        }

        return $uitpasCollection;
    }
}
