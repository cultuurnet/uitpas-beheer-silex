<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;

class InAnyOfCardSystems implements UiTPASSpecificationInterface
{
    /**
     * @var CardSystemCollection
     */
    private $cardSystemCollection;

    /**
     * @param CardSystemCollection $cardSystemCollection
     */
    public function __construct(CardSystemCollection $cardSystemCollection)
    {
        $this->cardSystemCollection = $cardSystemCollection;
    }

    /**
     * @param UiTPAS $uitpas
     * @return bool
     */
    public function isSatisfiedBy(UiTPAS $uitpas)
    {
        /* @var CardSystem $cardSystem */
        foreach ($this->cardSystemCollection as $cardSystem) {
            if ($uitpas->getCardSystem()->getId()->sameValueAs($cardSystem->getId())) {
                return true;
            }
        }

        return false;
    }
}
