<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut\Filter;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutCollection;
use CultuurNet\UiTPASBeheer\KansenStatuut\Specifications\KansenStatuutSpecificationInterface;

class KansenStatuutSpecificationFilter implements KansenStatuutFilterInterface
{
    /**
     * @var KansenStatuutSpecificationInterface
     */
    private $specification;

    /**
     * @param KansenStatuutSpecificationInterface $specification
     */
    public function __construct(KansenStatuutSpecificationInterface $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @param KansenStatuutCollection $kansenStatuutCollection
     * @return KansenStatuutCollection
     */
    public function filter(KansenStatuutCollection $kansenStatuutCollection)
    {
        /* @var KansenStatuut $kansenStatuut */
        foreach ($kansenStatuutCollection as $kansenStatuut) {
            if (!$this->specification->isSatisfiedBy($kansenStatuut)) {
                $kansenStatuutCollection = $kansenStatuutCollection->without($kansenStatuut);
            }
        }

        return $kansenStatuutCollection;
    }
}
