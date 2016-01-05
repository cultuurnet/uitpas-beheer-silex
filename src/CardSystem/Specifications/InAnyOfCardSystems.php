<?php

namespace CultuurNet\UiTPASBeheer\CardSystem\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\BelongsToCardSystemInterface;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;

class InAnyOfCardSystems
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
     * @param BelongsToCardSystemInterface $object
     * @return bool
     */
    public function isSatisfiedBy(BelongsToCardSystemInterface $object)
    {
        /* @var CardSystem $cardSystem */
        foreach ($this->cardSystemCollection as $cardSystem) {
            if ($object->getCardSystem()->getId()->sameValueAs($cardSystem->getId())) {
                return true;
            }
        }

        return false;
    }
}
