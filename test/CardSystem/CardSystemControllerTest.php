<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Price\Inquiry;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\Properties\VoucherType;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Money\Currency;
use ValueObjects\Money\CurrencyCode;
use ValueObjects\Money\Money;
use ValueObjects\Number\Integer;
use ValueObjects\Person\Age;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CardSystemController
     */
    protected $controller;

    /**
     * @var CardSystemServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->service = $this->getMock(CardSystemServiceInterface::class);
        $this->controller = new CardSystemController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_the_price_for_upgrade_with_a_cardsystem()
    {
        $request = new Request(
            [
                'date_of_birth' => '1991-04-23',
                'postal_code' => '3000',
                'voucher_number' => '2000000113',
            ]
        );
        $cardSystemId = '5';

        $inquiry = (new Inquiry(
            new CardSystemId($cardSystemId)
        ))->withDateOfBirth(
            new Date(
                new Year('1991'),
                Month::getByName('APRIL'),
                new MonthDay('23')
            )
        )->withPostalCode(
            new StringLiteral('3000')
        )->withVoucherNumber(
            new VoucherNumber('2000000113')
        );

        $price = (new Price(
            new Money(
                new Integer(500),
                new Currency(
                    CurrencyCode::fromNative('EUR')
                )
            ),
            false
        ))->withAgeRange(
            new AgeRange(
                new Age(18)
            )
        )->withVoucherType(
            new VoucherType(
                new StringLiteral('Bevraging vrijetijdsactiviteiten'),
                new StringLiteral('200')
            )
        );

        $this->service->expects($this->once())
            ->method('getPrice')
            ->with($inquiry)
            ->willReturn($price);

        $response = $this->controller->getPrice($request, $cardSystemId);

        $json = $response->getContent();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/price.json',
            $json
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_an_unknown_parameter_is_provided_for_a_price_inquiry()
    {
        $this->setExpectedException(UnknownParameterException::class);
        $this->controller->getPrice(
            new Request(
                [
                    'foo' => 'bar',
                ]
            ),
            '3'
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_date_of_birth_parameter_is_invalid()
    {
        $this->setExpectedException(IncorrectParameterValueException::class);
        $this->controller->getPrice(
            new Request(
                [
                    'date_of_birth' => 'x',
                ]
            ),
            '3'
        );
    }
}
