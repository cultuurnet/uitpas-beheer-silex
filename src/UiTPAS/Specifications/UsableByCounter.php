<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;
use CultuurNet\UiTPASBeheer\CardSystem\Specifications\InAnyOfCardSystems;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;

class UsableByCounter implements UiTPASSpecificationInterface
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
     * @param UiTPAS $uitpas
     * @return bool
     */
    public function isSatisfiedBy(UiTPAS $uitpas)
    {
        return $this->cardSystemSpecification->isSatisfiedBy($uitpas);
    }
}
