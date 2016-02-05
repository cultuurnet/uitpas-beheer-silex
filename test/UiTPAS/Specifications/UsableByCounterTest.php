<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\CardSystem\Specifications\InAnyOfCardSystems;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\StringLiteral\StringLiteral;

class UsableByCounterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas_Counter_Employee
     */
    protected $counter;

    /**
     * @var InAnyOfCardSystems
     */
    protected $specification;

    public function setUp()
    {
        $this->counter = new \CultureFeed_Uitpas_Counter_Employee();
        $this->counter->cardSystems[] = new \CultureFeed_Uitpas_CardSystem(1, 'Bluh');
        $this->counter->cardSystems[] = new \CultureFeed_Uitpas_CardSystem(2, 'Bloh');

        $this->specification = new UsableByCounter($this->counter);
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_an_uitpas_with_a_cardsystem_id_as_on_the_counter()
    {
        $uitpas = $this->createUiTPAS(new CardSystemId('1'));
        $this->assertTrue($this->specification->isSatisfiedBy($uitpas));

        $uitpas = $this->createUiTPAS(new CardSystemId('2'));
        $this->assertTrue($this->specification->isSatisfiedBy($uitpas));
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_an_uitpas_with_a_cardsystem_id_not_on_the_counter()
    {
        $cardSystem = $this->createUiTPAS(new CardSystemId('3'));
        $this->assertFalse($this->specification->isSatisfiedBy($cardSystem));
    }

    /**
     * @param CardSystemId $id
     * @return UiTPAS
     */
    private function createUiTPAS(CardSystemId $id)
    {
        return new UiTPAS(
            new UiTPASNumber('4567345678910'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                $id,
                new StringLiteral('Meh')
            )
        );
    }
}
