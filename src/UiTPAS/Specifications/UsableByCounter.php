<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;

class UsableByCounter extends InAnyOfCardSystems
{
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

        parent::__construct($cardSystemCollection);
    }
}
