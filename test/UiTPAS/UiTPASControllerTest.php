<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Exception\MissingParameterException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use CultuurNet\UiTPASBeheer\UiTPAS\Properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\Properties\VoucherType;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\RegistrationJsonDeserializer;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\RegistrationTestDataTrait;
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

class UiTPASControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use RegistrationTestDataTrait;

    /**
     * @var UiTPASServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    /**
     * @var UiTPASController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(UiTPASServiceInterface::class);

        $this->registrationJsonDeserializer = new RegistrationJsonDeserializer(
            new KansenStatuutJsonDeserializer()
        );

        $this->controller = new UiTPASController(
            $this->service,
            $this->registrationJsonDeserializer
        );
    }

    /**
     * @test
     */
    public function it_responds_an_uitpas_after_blocking_it()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');

        $uitpas = new UiTPAS(
            $uitpasNumber,
            UiTPASStatus::BLOCKED(),
            UiTPASType::STICKER(),
            new CardSystem(
                new CardSystemId('15'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $this->service->expects($this->once())
            ->method('block')
            ->with($uitpasNumber);

        $this->service->expects($this->once())
            ->method('get')
            ->with($uitpasNumber)
            ->willReturn($uitpas);

        $response = $this->controller->block($uitpasNumber->toNative());
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'UiTPAS/data/uitpas-blocked.json');
    }

    /**
     * @test
     */
    public function it_responds_an_uitpas_after_registering_it_to_a_passholder()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');
        $json = file_get_contents(__DIR__ . '/data/registration-complete.json');
        $request = new Request([], [], [], [], [], [], $json);

        $uitpas = new UiTPAS(
            $uitpasNumber,
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $this->service->expects($this->once())
            ->method('register')
            ->with(
                $uitpasNumber,
                $this->getCompleteRegistration()
            );

        $this->service->expects($this->once())
            ->method('get')
            ->with($uitpasNumber)
            ->willReturn($uitpas);

        $response = $this->controller->register(
            $request,
            $uitpasNumber->toNative()
        );

        $this->assertJsonEquals(
            $response->getContent(),
            'UiTPAS/data/uitpas-minimal.json'
        );
    }

    /**
     * @test
     */
    public function it_responds_the_price_for_an_uitpas_given_certain_parameters()
    {
        $request = new Request(
            [
                'reason' => 'FIRST_CARD',
                'date_of_birth' => '1991-04-23',
                'postal_code' => '3000',
                'voucher_number' => '2000000113',
            ]
        );
        $uitpasNumber = '0930000420206';

        $inquiry = (new Inquiry(
            new UiTPASNumber('0930000420206'),
            PurchaseReason::FIRST_CARD()
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
            false,
            new AgeRange(
                new Age(18)
            )
        ))->withVoucherType(
            new VoucherType(
                new StringLiteral('Bevraging vrijetijdsactiviteiten'),
                new StringLiteral('200')
            )
        );

        $this->service->expects($this->once())
            ->method('getPrice')
            ->with($inquiry)
            ->willReturn($price);

        $response = $this->controller->getPrice($request, $uitpasNumber);

        $json = $response->getContent();
        $this->assertJsonEquals($json, 'UiTPAS/data/price.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_the_required_reason_parameter_is_missing_for_a_price_inquiry()
    {
        $this->setExpectedException(MissingParameterException::class);
        $this->controller->getPrice(
            new Request(),
            '0930000420206'
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
                    'reason' => 'FIRST_CARD',
                    'foo' => 'bar',
                ]
            ),
            '0930000420206'
        );
    }
}
