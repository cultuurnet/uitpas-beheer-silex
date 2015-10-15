<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Specifications;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\StringLiteral\StringLiteral;

class NumberIsAnyOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UiTPASNumberCollection
     */
    protected $uitpasNumbers;

    /**
     * @var NumberIsAnyOf
     */
    protected $specification;

    public function setUp()
    {
        $this->uitpasNumbers = (new UiTPASNumberCollection())
            ->with(new UiTPASNumber('4567345678910'))
            ->with(new UiTPASNumber('4567345678902'));

        $this->specification = new NumberIsAnyOf($this->uitpasNumbers);
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_an_uitpas_with_a_number_as_in_the_collection()
    {
        $uitpas = $this->createUiTPAS(new UiTPASNumber('4567345678910'));
        $this->assertTrue($this->specification->isSatisfiedBy($uitpas));

        $uitpas = $this->createUiTPAS(new UiTPASNumber('4567345678902'));
        $this->assertTrue($this->specification->isSatisfiedBy($uitpas));
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_an_uitpas_with_a_number_not_in_the_collection()
    {
        $uitpas = $this->createUiTPAS(new UiTPASNumber('1256789944516'));
        $this->assertFalse($this->specification->isSatisfiedBy($uitpas));
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return UiTPAS
     */
    private function createUiTPAS(UiTPASNumber $uitpasNumber)
    {
        return new UiTPAS(
            $uitpasNumber,
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('1'),
                new StringLiteral('Blah')
            )
        );
    }
}
