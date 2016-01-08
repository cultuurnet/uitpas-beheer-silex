<?php

namespace CultuurNet\UiTPASBeheer\CardSystem\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\BelongsToCardSystemInterface;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;

class UsableByCounter
{
    /**
     * @var InAnyOfCardSystems
     */
    private $cardSystemSpecification;

    /**
     * @param \CultureFeed_Uitpas_Counter_Employee $cfCounterEmployee
     */
    public function __construct(\CultureFeed_Uitpas_Counter_Employee $cfCounterEmployee)
    {
        $cardSystemCollection = new CardSystemCollection();

        $cfCardSystems = $cfCounterEmployee->cardSystems;
        foreach ($cfCardSystems as $cfCardSystem) {
            $cardSystem = CardSystem::fromCultureFeedCardSystem($cfCardSystem);
            $cardSystemCollection = $cardSystemCollection->with($cardSystem);
        }

        $this->cardSystemSpecification = new InAnyOfCardSystems($cardSystemCollection);
    }

    /**
     * @param BelongsToCardSystemInterface $object
     * @return bool
     */
    public function isSatisfiedBy(BelongsToCardSystemInterface $object)
    {
        return $this->cardSystemSpecification->isSatisfiedBy($object);
    }
}
