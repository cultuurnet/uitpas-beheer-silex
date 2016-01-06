<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Identity\Identity;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedResultSet;
use CultuurNet\UiTPASBeheer\PassHolder\Search\Query;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderIteratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $passHolderService;

    /**
     * @var Query
     */
    private $searchQuery;

    /**
     * @var PassHolderIteratorFactory
     */
    private $passHolderIteratorFactory;

    public function setUp()
    {
        $this->passHolderService = $this->getMock(PassHolderServiceInterface::class);
        $this->searchQuery = new Query();

        $this->passHolderIteratorFactory = $this->getMock(
            PassHolderIteratorFactory::class,
            null,
            array($this->passHolderService)
        );
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_With_parameters()
    {
        $passHolderIteratorFactory = new PassHolderIteratorFactory($this->passHolderService);
        $expectedPassHolderIteratorFactory = new PassHolderIteratorFactory($this->passHolderService, 20);

        $this->assertEquals($expectedPassHolderIteratorFactory, $passHolderIteratorFactory);
    }

    /**
     * @test
     */
    public function it_can_search_with_a_querybuilder()
    {
        $identityA = new Identity(
            new UiTPAS(
                new UiTPASNumber('0930000802619'),
                UiTPASStatus::ACTIVE(),
                UiTPASType::CARD(),
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('Card system A')
                )
            )
        );

        $passholderA = new PassHolder(
            new Name(
                new StringLiteral('John'),
                new StringLiteral(''),
                new StringLiteral('Doe')
            ),
            new Address(
                new StringLiteral('3000'),
                new StringLiteral('Leuven')
            ),
            new BirthInformation(
                new Date(
                    new Year(1990),
                    Month::JANUARY(),
                    new MonthDay(1)
                )
            )
        );

        $identityA = $identityA->withPassHolder($passholderA);

        $identityB = new Identity(
            new UiTPAS(
                new UiTPASNumber('3330047460116'),
                UiTPASStatus::ACTIVE(),
                UiTPASType::CARD(),
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('Card system A')
                )
            )
        );

        $passholderB = new PassHolder(
            new Name(
                new StringLiteral('Jane'),
                new StringLiteral(''),
                new StringLiteral('Doe')
            ),
            new Address(
                new StringLiteral('3000'),
                new StringLiteral('Leuven')
            ),
            new BirthInformation(
                new Date(
                    new Year(1990),
                    Month::MARCH(),
                    new MonthDay(21)
                )
            )
        );

        $identityB = $identityB->withPassHolder($passholderB);

        $results = new PagedResultSet(
            new Integer(2),
            [
                $identityA,
                $identityB,
            ]
        );

        $this->passHolderService->expects($this->once())
            ->method('search')
            ->with(
                $this->searchQuery->withPagination(
                    new Integer(1),
                    new Integer(20)
                )
            )
            ->willReturn($results);

        $searchResults = $this->passHolderIteratorFactory->search($this->searchQuery);
        $results = iterator_to_array($searchResults);

        $expectedResults = array(
            '0930000802619' => $passholderA,
            '3330047460116' => $passholderB,
        );

        $this->assertEquals($expectedResults, $results);
    }
}
