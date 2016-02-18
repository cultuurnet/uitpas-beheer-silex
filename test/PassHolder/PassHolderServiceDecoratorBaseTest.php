<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedResultSet;
use CultuurNet\UiTPASBeheer\PassHolder\Search\Query;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Identity\UUID;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderServiceDecoratorBaseTest extends PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var PassHolderServiceDecoratorBase
     */
    private $decorator;

    /**
     * @var PassHolderServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $decoratee;

    /**
     * @var UiTPASNumber
     */
    private $uitpasNumber;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->decoratee = $this->getMock(PassHOlderServiceInterface::class);

        $this->decorator = $this->getMockForAbstractClass(
            PassHolderServiceDecoratorBase::class,
            [$this->decoratee]
        );

        $this->uitpasNumber = new UiTPASNumber('0930000343119');
    }

    /**
     * @test
     */
    public function passes_through_calls_to_getByUitpasNumber_to_the_decoratee()
    {
        $passHolder = $this->getCompletePassHolder();

        $this->decoratee->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($this->uitpasNumber)
            ->willReturn($passHolder);

        $actualPassHolder = $this->decorator->getByUitpasNumber(
            $this->uitpasNumber
        );

        $this->assertEquals(
            $passHolder,
            $actualPassHolder
        );
    }


    /**
     * @test
     */
    public function passes_through_calls_to_search_to_the_decoratee()
    {
        $resultSet = new PagedResultSet(new Integer(0), []);

        $query = (new Query())->withFirstName(new StringLiteral('John'));

        $this->decoratee->expects($this->once())
            ->method('search')
            ->with($query)
            ->willReturn($resultSet);

        $actualResultSet = $this->decorator->search($query);

        $this->assertEquals($resultSet, $actualResultSet);
    }

    /**
     * @test
     */
    public function passes_through_calls_to_update_to_the_decoratee()
    {
        $passHolderWithUpdates = $this->getCompletePassHolder();

        $expectedPassHolder = $passHolderWithUpdates
            ->withRemarks(
                new Remarks('simultaneous changes from elsewhere')
            );

        $this->decoratee->expects($this->once())
            ->method('update')
            ->with(
                $this->uitpasNumber,
                $passHolderWithUpdates
            )
            ->willReturn($expectedPassHolder);

        $passHolder = $this->decorator->update(
            $this->uitpasNumber,
            $passHolderWithUpdates
        );

        $this->assertEquals($expectedPassHolder, $passHolder);
    }

    /**
     * @test
     */
    public function passes_through_calls_to_upgradeCardSystems_to_the_decoratee()
    {
        $cardSystemUpgrade = CardSystemUpgrade::withoutNewUiTPAS(
            new CardSystemId('1')
        );

        $this->decoratee->expects($this->once())
            ->method('upgradeCardSystems')
            ->with($this->uitpasNumber, $cardSystemUpgrade);

        $this->decorator->upgradeCardSystems(
            $this->uitpasNumber,
            $cardSystemUpgrade
        );
    }

    /**
     * @test
     */
    public function passes_through_calls_to_register_to_the_decoratee()
    {
        $passHolder = $this->getCompletePassHolder();
        $voucher = new VoucherNumber('free-ticket-to-ride');
        $kansenStatuut = new KansenStatuut(
            new Date(
                new Year(2016),
                Month::getByName('APRIL'),
                new MonthDay(1)
            )
        );

        $expectedUuid = new UUID('19beddbe-ec39-49cb-8ef7-b3ac9f59f952');

        $this->decoratee->expects($this->once())
            ->method('register')
            ->with(
                $this->uitpasNumber,
                $passHolder,
                $voucher,
                $kansenStatuut
            )
            ->willReturn($expectedUuid);

        $uuid = $this->decorator->register(
            $this->uitpasNumber,
            $passHolder,
            $voucher,
            $kansenStatuut
        );

        $this->assertEquals($expectedUuid, $uuid);
    }
}
