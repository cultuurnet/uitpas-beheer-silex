<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Filter;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\UiTPAS\Specifications\UiTPASSpecificationInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASSpecificationFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UiTPASSpecificationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $specification;

    /**
     * @var UiTPASSpecificationFilter
     */
    protected $filter;

    /**
     * @var UiTPASCollection
     */
    protected $uitpasCollection;

    /**
     * @var UiTPAS
     */
    protected $activeUitpas;

    /**
     * @var UiTPAS
     */
    protected $blockedUitpas;

    public function setUp()
    {
        $this->specification = $this->getMock(UiTPASSpecificationInterface::class);
        $this->filter = new UiTPASSpecificationFilter($this->specification);

        $this->activeUitpas = new UiTPAS(
            new UiTPASNumber('4567345678910'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('1'),
                new StringLiteral('Foo')
            )
        );

        $this->blockedUitpas = new UiTPAS(
            new UiTPASNumber('4567345678902'),
            UiTPASStatus::BLOCKED(),
            UiTPASType::KEY(),
            new CardSystem(
                new CardSystemId('2'),
                new StringLiteral('Bar')
            )
        );

        $this->uitpasCollection = (new UiTPASCollection())
            ->with($this->activeUitpas)
            ->with($this->blockedUitpas);
    }

    /**
     * @test
     */
    public function it_filters_out_any_uitpas_that_does_not_satisfy_the_specification()
    {
        // The mock specification should only be satisfied by an active uitpas.
        $this->specification->expects($this->any())
            ->method('isSatisfiedBy')
            ->willReturnCallback(function (UiTPAS $uitpas) {
                return $uitpas->getStatus()->sameValueAs(UiTPASStatus::ACTIVE());
            });

        $expected = (new UiTPASCollection())
            ->with($this->activeUitpas);

        $actual = $this->filter->filter($this->uitpasCollection);

        $this->assertEquals($expected, $actual);
    }
}
