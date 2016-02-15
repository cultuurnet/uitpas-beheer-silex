<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\Specifications\UsableByCounter as CardSystemUsableByCounter;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;

class UsableByCounter implements UiTPASSpecificationInterface
{
    /**
     * @var CardSystemUsableByCounter
     */
    private $cardSystemSpecification;

    /**
     * UsableByCounter constructor.
     * @param \CultureFeed_Uitpas_Counter_Employee $cfCounterEmployee
     */
    public function __construct(\CultureFeed_Uitpas_Counter_Employee $cfCounterEmployee)
    {
        $this->cardSystemSpecification = new CardSystemUsableByCounter($cfCounterEmployee);
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
