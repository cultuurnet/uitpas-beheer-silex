<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\Export\FileWriterInterface;
use CultuurNet\UiTPASBeheer\Identity\Identity;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\MembershipStatus;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\AddressJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\NameJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferencesJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedResultSet;
use CultuurNet\UiTPASBeheer\PassHolder\Search\Query;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class PassHolderControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use PassHolderDataTrait;

    /**
     * @var PassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var PassHolderIteratorFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $passHolderIteratorFactory;

    /**
     * @var FileWriterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $exportFileWriter;

    /**
     * @var PassHolderJsonDeserializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $passholderDeserializer;

    /**
     * @var RegistrationJsonDeserializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registrationDeserializer;

    /**
     * @var CardSystemUpgradeJsonDeserializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cardSystemUpgradeJsonDeserializer;

    /**
     * @var Query
     */
    protected $searchQuery;

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGenerator;

    /**
     * @var PassHolderController
     */
    protected $controller;

    /**
     * @var string
     */
    protected $counterConsumerKey;

    /**
     * @var \CultureFeed_Uitpas_Counter_EmployeeCardSystem
     */
    protected $counterCardSystem;

    /**
     * @var \CultureFeed_Uitpas_Counter_Employee
     */
    protected $counter;

    public function setUp()
    {
        $this->service = $this->getMock(PassHolderServiceInterface::class);

        $this->passHolderIteratorFactory = $this->getMock(PassHolderIteratorFactoryInterface::class);

        $this->exportFileWriter = $this->getMock(FileWriterInterface::class);

        $this->passholderDeserializer = new PassHolderJsonDeserializer(
            new NameJsonDeserializer(),
            new AddressJsonDeserializer(),
            new BirthInformationJsonDeserializer(),
            new ContactInformationJsonDeserializer(),
            new PrivacyPreferencesJsonDeserializer()
        );

        $this->registrationDeserializer = new RegistrationJsonDeserializer(
            $this->passholderDeserializer,
            new KansenStatuutJsonDeserializer()
        );

        $this->cardSystemUpgradeJsonDeserializer = new CardSystemUpgradeJsonDeserializer(
            new KansenStatuutJsonDeserializer()
        );

        $this->searchQuery = new Query();

        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);

        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->counterCardSystem = new \CultureFeed_Uitpas_Counter_EmployeeCardSystem();
        $this->counterCardSystem->id = 30;
        $this->counterCardSystem->name = 'UiTPAS Regio Brussel';

        $this->counter = new \CultureFeed_Uitpas_Counter_Employee();
        $this->counter->consumerKey = $this->counterConsumerKey->toNative();
        $this->counter->cardSystems = array($this->counterCardSystem);

        $this->controller = new PassHolderController(
            $this->service,
            $this->passHolderIteratorFactory,
            $this->exportFileWriter,
            $this->passholderDeserializer,
            $this->registrationDeserializer,
            $this->cardSystemUpgradeJsonDeserializer,
            $this->searchQuery,
            $this->urlGenerator,
            $this->counter
        );
    }

    /**
     * @test
     */
    public function it_responds_search_results_for_a_set_of_uitpas_numbers()
    {
        $request = new Request(
            [
                'uitpasNumber' => [
                    '0930000420206',
                    '0930000237915',
                ],
            ]
        );

        $expectedQuery = (new Query())
            ->withUiTPASNumbers(
                (new UiTPASNumberCollection())
                    ->with(new UiTPASNumber('0930000420206'))
                    ->with(new UiTPASNumber('0930000237915'))
            )
            ->withPagination(
                new Integer(1),
                new Integer(10)
            );

        $uitpas = new UiTPAS(
            new UiTPASNumber('0930000420206'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('2'),
                new StringLiteral('Uitpas regio Landen')
            )
        );

        $passHolder = $this->getCompletePassHolder()
            ->withUiTPASCollection(
                (new UiTPASCollection())
                    ->with($uitpas)
            );

        $identity = (new Identity($uitpas))
            ->withPassHolder($passHolder);

        $pagedResultSet = (new PagedResultSet(
            new Integer(30),
            [$identity]
        ))->withInvalidUiTPASNumbers(
            (new UiTPASNumberCollection())
                ->with(new UiTPASNumber('0930000237915'))
        );

        $this->service->expects($this->once())
            ->method('search')
            ->with($expectedQuery)
            ->willReturn($pagedResultSet);

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->willReturnCallback(
                function ($routeName, $query) {
                    return 'http://foo.bar/search?' . http_build_query($query);
                }
            );

        $response = $this->controller->search($request);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/search/paged-collection.json');
    }

    /**
     * @test
     */
    public function it_responds_search_results_for_a_single_uitpas_number()
    {
        $request = new Request(['uitpasNumber' => '0930000420206']);

        $expectedQuery = (new Query())
            ->withUiTPASNumbers(
                (new UiTPASNumberCollection())
                    ->with(new UiTPASNumber('0930000420206'))
            )
            ->withPagination(
                new Integer(1),
                new Integer(10)
            );

        $this->service->expects($this->once())
            ->method('search')
            ->with($expectedQuery)
            ->willReturn(new PagedResultSet(new Integer(0), []));

        $this->controller->search($request);
    }


    /**
     * @test
     */
    public function it_responds_search_results_for_passholder_info_parameters()
    {
        $request = new Request(
            [
                'dateOfBirth' => '1991-04-23',
                'firstName' => 'John',
                'name' => 'Do*',
                'street' => 'Vaartkom',
                'city' => 'Leuven',
                'email' => 'john@doe.com',
                'membershipAssociationId' => '5',
                'membershipStatus' => 'ACTIVE',
            ]
        );

        $expectedQuery = (new Query())
            ->withDateOfBirth(
                Date::fromNativeDateTime(
                    \DateTime::createFromFormat('Y-m-d', '1991-04-23')
                )
            )
            ->withFirstName(
                new StringLiteral('John')
            )
            ->withName(
                new StringLiteral('Do*')
            )
            ->withStreet(
                new StringLiteral('Vaartkom')
            )
            ->withCity(
                new StringLiteral('Leuven')
            )
            ->withEmail(
                new EmailAddress('john@doe.com')
            )
            ->withAssociationId(
                new AssociationId('5')
            )
            ->withMembershipStatus(
                MembershipStatus::ACTIVE()
            );

        $this->service->expects($this->once())
            ->method('search')
            ->with($expectedQuery)
            ->willReturn(
                new PagedResultSet(new Integer(0), [])
            );

        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_responds_a_specific_page_of_search_results()
    {
        $request = new Request(
            [
                'page' => '2',
                'limit' => '100',
            ]
        );

        $expectedQuery = (new Query())
            ->withPagination(
                new Integer(2),
                new Integer(100)
            );

        $this->service->expects($this->once())
            ->method('search')
            ->with($expectedQuery)
            ->willReturn(new PagedResultSet(new Integer(0), []));

        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_searching_for_an_unknown_parameter()
    {
        $request = new Request(['unknown' => 'foo']);
        $this->setExpectedException(UnknownParameterException::class);
        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_searching_by_invalid_formatted_uitpasnumbers()
    {
        // First uitpas number is valid, the others are invalid.
        $uitpasNumbers = [
            '0930000420206',
            '093000042020',
            '09A3B004202066',
            '0930000237912',
        ];

        $invalidUitpasNumbers = $uitpasNumbers;
        array_shift($invalidUitpasNumbers);

        $request = new Request(
            [
                'uitpasNumber' => $uitpasNumbers,
            ]
        );

        try {
            $this->controller->search($request);
            $this->fail('search was expected to throw UiTPASNumberInvalidException');
        } catch (IncorrectParameterValueException $e) {
            $this->assertEquals($invalidUitpasNumbers, $e->getContext());
            $this->assertEquals('INVALID_UITPAS_NUMBER', $e->getReadableCode());
        }
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_searching_by_invalid_date_of_birth()
    {
        $request = new Request(['dateOfBirth' => '01/01/1970']);
        $this->setExpectedException(IncorrectParameterValueException::class);
        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_searching_by_invalid_email()
    {
        $request = new Request(['email' => 'john.doe']);
        $this->setExpectedException(IncorrectParameterValueException::class);
        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_searching_by_invalid_membership_status()
    {
        $request = new Request(['membershipStatus' => 'I_AM_REGISTERED']);
        $this->setExpectedException(IncorrectParameterValueException::class);
        $this->controller->search($request);
    }

    /**
     * @test
     */
    public function it_responds_the_passholder_matching_a_provided_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($this->getCompletePassHolder());

        $response = $this->controller->getByUitpasNumber($uitpasNumberValue);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder-complete.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_passholder_can_not_be_found_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn(null);

        try {
            $this->controller->getByUitpasNumber($uitpasNumberValue);
            $this->fail('getByUitpasNumber() should throw PassHolderNotFoundException.');
        } catch (PassHolderNotFoundException $exception) {
            $this->assertInstanceOf(ReadableCodeExceptionInterface::class, $exception);
            $this->assertNotEmpty($exception->getReadableCode());
        }
    }

    /**
     * @test
     */
    public function it_updates_a_given_passholder_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $data = file_get_contents(__DIR__ . '/data/passholder-update.json');
        $request = new Request([], [], [], [], [], [], $data);

        $this->service->expects($this->once())
            ->method('update')
            ->with($uitpasNumber, $this->getCompletePassHolderUpdate());

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($this->getCompletePassHolder());

        $response = $this->controller->update($request, $uitpasNumberValue);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder-complete.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_passholder_can_not_be_updated()
    {
        $uitpasNumberValue = '0930000125607';

        $data = file_get_contents(__DIR__ . '/data/passholder-minimum.json');
        $request = new Request([], [], [], [], [], [], $data);

        $message = 'Something went wrong.';
        $code = 'SOMETHING_WRONG';

        $this->service->expects($this->once())
            ->method('update')
            ->willThrowException(new \CultureFeed_Exception($message, $code));

        $this->setExpectedException(CompleteResponseException::class);
        $this->controller->update($request, $uitpasNumberValue);
    }

    /**
     * @test
     */
    public function it_upgrades_the_passholder_cardsystems()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $data = file_get_contents(__DIR__ . '/data/cardsystem-upgrade-without-new-uitpas.json');
        $request = new Request([], [], [], [], [], [], $data);

        $expectedUpgrade = CardSystemUpgrade::withoutNewUiTPAS(new CardSystemId('1'))
            ->withVoucherNumber(
                new VoucherNumber('free ticket to ride')
            );

        $this->service->expects($this->once())
            ->method('upgradeCardSystems')
            ->with(
                $uitpasNumber,
                $expectedUpgrade
            );

        $response = $this->controller->upgradeCardSystems(
            $request,
            $uitpasNumberValue
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_registers_passholder_with_an_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $data = file_get_contents(__DIR__ . '/data/passholder-registration.json');
        $request = new Request([], [], [], [], [], [], $data);

        $this->service->expects($this->once())
            ->method('register')
            ->with(
                $uitpasNumber,
                $this->getCompletePassHolderUpdate(),
                new VoucherNumber('i-am-voucher'),
                (new KansenStatuut(
                    new Date(
                        new Year('2345'),
                        Month::SEPTEMBER(),
                        new MonthDay(13)
                    )
                ))->withRemarks(
                    new Remarks(
                        'I am remarkable'
                    )
                )
            );

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($this->getCompletePassHolder());

        $response = $this->controller->register($request, $uitpasNumberValue);

        $this->assertJsonEquals($response->getContent(), 'PassHolder/data/passholder-complete.json');
    }

    /**
     * @test
     */
    public function it_returns_a_readable_error_when_failing_to_create_a_passholder()
    {
        $uitpasNumberValue = '0930000125607';

        $data = file_get_contents(__DIR__ . '/data/passholder-registration.json');
        $request = new Request([], [], [], [], [], [], $data);

        $message = 'Something went wrong.';
        $code = 'SOMETHING_WRONG';

        $this->service->expects($this->once())
            ->method('register')
            ->willThrowException(new \CultureFeed_Exception($message, $code));

        $this->setExpectedException(CompleteResponseException::class);
        $this->controller->register($request, $uitpasNumberValue);
    }

    /**
     * @test
     */
    public function it_can_export_passholders_from_a_selection()
    {
        $request = new Request(
            [
                'uitpasNumber' => [
                    '0930000420206',
                    '0930000237915',
                ],
            ]
        );

        $expectedQuery = (new Query())
            ->withUiTPASNumbers(
                (new UiTPASNumberCollection())
                    ->with(new UiTPASNumber('0930000420206'))
                    ->with(new UiTPASNumber('0930000237915'))
            )
            ->withPagination(
                new Integer(1),
                new Integer(10)
            );

        $uitpas = new UiTPAS(
            new UiTPASNumber('0930000420206'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('2'),
                new StringLiteral('Uitpas regio Landen')
            )
        );

        $passHolder = $this->getCompletePassHolder()
            ->withUiTPASCollection(
                (new UiTPASCollection())
                    ->with($uitpas)
            );

        $results = array('0930000420206' => $passHolder);

        $this->passHolderIteratorFactory->expects($this->once())
            ->method('search')
            ->with($expectedQuery)
            ->willReturn($results);

        $this->exportFileWriter->expects($this->once())
            ->method('getHttpHeaders')
            ->willReturn(array());

        $this->exportFileWriter->expects($this->once())
            ->method('open')
            ->willReturn('START' . PHP_EOL);

        $this->exportFileWriter->expects($this->once())
            ->method('close')
            ->willReturn('END');

        $this->exportFileWriter->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                [
                    [
                        'UiTPAS nummer',
                        'Naam',
                        'Voornaam',
                        'Geboortedatum',
                        'Geslacht',
                        'Adres',
                        'Postcode',
                        'Gemeente',
                        'Telefoon',
                        'GSM',
                        'Nationaliteit',
                        'Kansenstatuut einddatum',
                        'ID',
                    ],
                ],
                [
                    [
                        '0930000420206',
                        'Zyrani',
                        'Layla',
                        '13-09-1976',
                        'Vrouw',
                        'Rue Perdue 101 /0003',
                        '1090',
                        'Jette (Brussel)',
                        '0488694231',
                        '0499748596',
                        'Maroc',
                        '15-09-2016',
                        '5',
                    ],
                ]
            )
            ->willReturn('ROW' . PHP_EOL);

        $response = $this->controller->export($request);

        ob_start();
        $response->sendContent();
        $output = ob_get_clean();

        $expectedOutput = 'START'  . PHP_EOL .
            'ROW'  . PHP_EOL .
            'ROW'  . PHP_EOL .
            'END';

        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @test
     */
    public function it_can_export_passholders_from_query_parameters()
    {
        $request = new Request(
            [
                'selection' => [
                    '0930000420206',
                    '0930000237915',
                ],
            ]
        );

        $expectedQuery = (new Query())
            ->withUiTPASNumbers(
                (new UiTPASNumberCollection())
                    ->with(new UiTPASNumber('0930000420206'))
                    ->with(new UiTPASNumber('0930000237915'))
            )
            ->withPagination(
                new Integer(1),
                new Integer(10)
            );

        $uitpas = new UiTPAS(
            new UiTPASNumber('0930000420206'),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('2'),
                new StringLiteral('Uitpas regio Landen')
            )
        );

        $passHolder = $this->getCompletePassHolder()
            ->withUiTPASCollection(
                (new UiTPASCollection())
                    ->with($uitpas)
            );

        $passHolder = $passHolder->withoutContactInformation();

        $results = array('0930000420206' => $passHolder);

        $this->passHolderIteratorFactory->expects($this->once())
            ->method('search')
            ->with($expectedQuery)
            ->willReturn($results);

        $this->exportFileWriter->expects($this->once())
            ->method('getHttpHeaders')
            ->willReturn(array());

        $this->exportFileWriter->expects($this->once())
            ->method('open')
            ->willReturn('START' . PHP_EOL);

        $this->exportFileWriter->expects($this->once())
            ->method('close')
            ->willReturn('END');

        $this->exportFileWriter->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                [
                    [
                        'UiTPAS nummer',
                        'Naam',
                        'Voornaam',
                        'Geboortedatum',
                        'Geslacht',
                        'Adres',
                        'Postcode',
                        'Gemeente',
                        'Telefoon',
                        'GSM',
                        'Nationaliteit',
                        'Kansenstatuut einddatum',
                        'ID',
                    ],
                ],
                [
                    [
                        '0930000420206',
                        'Zyrani',
                        'Layla',
                        '13-09-1976',
                        'Vrouw',
                        'Rue Perdue 101 /0003',
                        '1090',
                        'Jette (Brussel)',
                        '',
                        '',
                        'Maroc',
                        '15-09-2016',
                        '5',
                    ],
                ]
            )
            ->willReturn('ROW' . PHP_EOL);

        $response = $this->controller->export($request);

        ob_start();
        $response->sendContent();
        $output = ob_get_clean();

        $expectedOutput = 'START'  . PHP_EOL .
            'ROW'  . PHP_EOL .
            'ROW'  . PHP_EOL .
            'END';

        $this->assertEquals($expectedOutput, $output);
    }
}
