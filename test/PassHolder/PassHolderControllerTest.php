<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\AddressJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\NameJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferencesJsonDeserializer;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;

class PassHolderControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use PassHolderDataTrait;

    /**
     * @var PassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var PassHolderJsonDeserializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $passholderDeserializer;

    /**
     * @var RegistrationJsonDeserializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registrationDeserializer;

    /**
     * @var PassHolderController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(PassHolderServiceInterface::class);

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

        $this->controller = new PassHolderController(
            $this->service,
            $this->passholderDeserializer,
            $this->registrationDeserializer
        );
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

        $this->setExpectedException(ReadableCodeResponseException::class);
        $this->controller->update($request, $uitpasNumberValue);
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
            ->with($uitpasNumber, $this->getCompletePassHolderUpdate());

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

        $this->setExpectedException(ReadableCodeResponseException::class);
        $this->controller->register($request, $uitpasNumberValue);
    }
}
