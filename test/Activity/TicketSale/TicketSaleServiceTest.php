<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\RegisteredTicketSale;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\TariffId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolder;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderNotFoundException;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\Properties\Address;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\DateTime\Hour;
use ValueObjects\DateTime\Minute;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Second;
use ValueObjects\DateTime\Time;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Natural;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class TicketSaleServiceTest extends \PHPUnit_Framework_TestCase
{
    use TicketSaleTestDataTrait;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var PassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $passHolderService;

    /**
     * @var TicketSaleService
     */
    protected $service;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    /**
     * @var Registration
     */
    protected $registration;

    /**
     * @var StringLiteral
     */
    protected $tariffId;

    /**
     * @var \CultureFeed_Uitpas_Event_TicketSale
     */
    protected $cfTicketSale;

    public function setUp()
    {
        $callOriginalConstructor = false;
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class, [], [], '', $callOriginalConstructor);

        $this->counterConsumerKey = new CounterConsumerKey('foo-bar');

        $this->passHolderService = $this->getMock(PassHolderServiceInterface::class);

        $this->service = new TicketSaleService(
            $this->uitpas,
            $this->counterConsumerKey,
            $this->passHolderService
        );

        $this->uitpasNumber = new UiTPASNumber('1000000600717');

        $this->registration = new Registration(
            new StringLiteral('100'),
            new PriceClass('Basisprijs')
        );

        $this->tariffId = new TariffId(141327894321897);

        $this->cfTicketSale = new \CultureFeed_Uitpas_Event_TicketSale();
        $this->cfTicketSale->id = 30818;
        $this->cfTicketSale->price = 2;
        $this->cfTicketSale->creationDate = 1440079102;
    }

    /**
     * @test
     */
    public function it_cancels_a_ticket_sale()
    {
        $ticketId = new StringLiteral('123');

        $this->uitpas->expects($this->once())
            ->method('cancelTicketSaleById')
            ->with(
                $ticketId->toNative(),
                $this->counterConsumerKey->toNative()
            );

        $this->service->cancel($ticketId);
    }

    /**
     * @test
     */
    public function it_registers_a_ticket_sale()
    {
        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->with(
                $this->uitpasNumber->toNative(),
                $this->registration->getActivityId()->toNative(),
                $this->counterConsumerKey->toNative(),
                $this->registration->getPriceClass()->toNative(),
                null,
                null
            )
            ->willReturn($this->cfTicketSale);

        $expected = new RegisteredTicketSale(
            new StringLiteral('30818'),
            new Real(2.0),
            new DateTime(
                new Date(
                    new Year(2015),
                    Month::getByName('AUGUST'),
                    new MonthDay('20')
                ),
                new Time(
                    new Hour(13),
                    new Minute(58),
                    new Second(22)
                )
            )
        );

        $actual = $this->service->register(
            $this->uitpasNumber,
            $this->registration
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_uses_the_tariff_id_if_provided_when_registering_a_ticket_sale()
    {
        $this->registration = $this->registration
            ->withTariffId($this->tariffId);

        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->with(
                $this->uitpasNumber->toNative(),
                $this->registration->getActivityId()->toNative(),
                $this->counterConsumerKey->toNative(),
                $this->registration->getPriceClass()->toNative(),
                $this->tariffId->toNative(),
                null
            )
            ->willReturn($this->cfTicketSale);

        $this->service->register(
            $this->uitpasNumber,
            $this->registration
        );
    }

    /**
     * @test
     */
    public function it_uses_the_amount_if_provided_when_registering_a_ticket_sale()
    {
        $this->registration = $this->registration
            ->withAmount(new Natural(3));

        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->with(
                $this->uitpasNumber->toNative(),
                $this->registration->getActivityId()->toNative(),
                $this->counterConsumerKey->toNative(),
                $this->registration->getPriceClass()->toNative(),
                null,
                3
            )
            ->willReturn($this->cfTicketSale);

        $this->service->register(
            $this->uitpasNumber,
            $this->registration
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_ticket_sale_registration_failed()
    {
        $expectedMessage = 'Pashouder ticket sales:1. Toegelaten ticketSales: 1 / Absoluut';
        $expectedCode = 'MAXIMUM_REACHED';

        $cfException = new \CultureFeed_Exception(
            $expectedMessage,
            $expectedCode
        );

        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->willThrowException($cfException);

        // We can't use setExpectedException here because we need to check the
        // readable error code.
        try {
            $this->service->register(
                $this->uitpasNumber,
                $this->registration
            );
            $this->fail('A CompleteResponseException should have been thrown.');
        } catch (CompleteResponseException $e) {
            $this->assertEquals($expectedMessage, $e->getMessage());
            $this->assertEquals($expectedCode, $e->getReadableCode());
            $this->assertEquals(400, $e->getCode());
        } catch (\Exception $e) {
            $this->fail(
                'A CompleteResponseException should have been thrown. Caught ' . get_class($e) . ' instead.'
            );
        }
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_no_passholder_is_found_for_an_uitpas_number()
    {
        $this->passHolderService->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($this->uitpasNumber)
            ->willReturn(null);

        $this->setExpectedException(PassHolderNotFoundException::class);

        $this->service->getByUiTPASNumber($this->uitpasNumber);
    }

    /**
     * @test
     */
    public function it_returns_a_list_of_ticket_sales_by_uitpas_number()
    {
        $uid = new Uid('123456');

        $passHolder = (new PassHolder(
            new Name(
                new StringLiteral('John'),
                new StringLiteral('Doe')
            ),
            new Address(
                new StringLiteral('3000'),
                new StringLiteral('Leuven')
            ),
            new BirthInformation(
                new Date(
                    new Year('2015'),
                    Month::getByName('SEPTEMBER'),
                    new MonthDay('9')
                )
            )
        ))->withUid($uid);

        $this->passHolderService->expects($this->once())
            ->method('getByUiTPASNumber')
            ->with($this->uitpasNumber)
            ->willReturn($passHolder);

        $expectedQuery = new \CultureFeed_Uitpas_Event_Query_SearchTicketSalesOptions();
        $expectedQuery->uid = $uid->toNative();
        $expectedQuery->balieConsumerKey = $this->counterConsumerKey->toNative();

        $cfTicketSaleA = new \CultureFeed_Uitpas_Event_TicketSale();
        $cfTicketSaleA->id = 'aaa';
        $cfTicketSaleA->nodeTitle = 'Lorem Ipsum';
        $cfTicketSaleA->tariff = 1.5;
        $cfTicketSaleA->creationDate = 1447174206;

        $cfTicketSaleB = new \CultureFeed_Uitpas_Event_TicketSale();
        $cfTicketSaleB->id = 'bbb';
        $cfTicketSaleB->nodeTitle = 'Dolor Sit Amet';
        $cfTicketSaleB->tariff = 2.0;
        $cfTicketSaleB->creationDate = 0;

        $cfResultSet = new \CultureFeed_ResultSet(2, [$cfTicketSaleA, $cfTicketSaleB]);

        $this->uitpas->expects($this->once())
            ->method('searchTicketSales')
            ->with($expectedQuery)
            ->willReturn($cfResultSet);

        $expected = $this->getTicketSaleHistory();

        $actual = $this->service->getByUiTPASNumber($this->uitpasNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_no_uid_is_found_for_an_uitpas_number()
    {
        $passHolder = new PassHolder(
            new Name(
                new StringLiteral('John'),
                new StringLiteral('Doe')
            ),
            new Address(
                new StringLiteral('3000'),
                new StringLiteral('Leuven')
            ),
            new BirthInformation(
                new Date(
                    new Year('2015'),
                    Month::getByName('SEPTEMBER'),
                    new MonthDay('9')
                )
            )
        );

        $this->passHolderService->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($this->uitpasNumber)
            ->willReturn($passHolder);

        $this->setExpectedException(PassHolderNotFoundException::class);

        $this->service->getByUiTPASNumber($this->uitpasNumber);
    }
}
