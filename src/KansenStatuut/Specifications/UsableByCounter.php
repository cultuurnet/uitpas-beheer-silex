<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\Specifications\UsableByCounter as CardSystemUsableByCounter;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;

class UsableByCounter implements KansenStatuutSpecificationInterface
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
     * @param KansenStatuut $kansenStatuut
     * @return bool
     */
    public function isSatisfiedBy(KansenStatuut $kansenStatuut)
    {
        return $this->cardSystemSpecification->isSatisfiedBy($kansenStatuut);
    }
}
