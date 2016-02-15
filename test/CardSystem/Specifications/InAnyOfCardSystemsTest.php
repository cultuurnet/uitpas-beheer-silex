<?php

namespace CultuurNet\UiTPASBeheer\CardSystem\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\StringLiteral\StringLiteral;

class InAnyOfCardSystemsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CardSystemCollection
     */
    protected $cardSystemCollection;

    /**
     * @var InAnyOfCardSystems
     */
    protected $specification;

    public function setUp()
    {
        $this->cardSystemCollection = (new CardSystemCollection())
            ->with(
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('Boo')
                )
            )
            ->with(
                new CardSystem(
                    new CardSystemId('2'),
                    new StringLiteral('Bah')
                )
            );

        $this->specification = new InAnyOfCardSystems($this->cardSystemCollection);
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_an_uitpas_with_a_cardsystem_id_as_in_the_collection()
    {
        $uitpas = $this->createUiTPAS(new CardSystemId('1'));
        $this->assertTrue($this->specification->isSatisfiedBy($uitpas));

        $uitpas = $this->createUiTPAS(new CardSystemId('2'));
        $this->assertTrue($this->specification->isSatisfiedBy($uitpas));
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_an_uitpas_with_a_cardsystem_id_not_in_the_collection()
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
