<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;

class PassHolderControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var PassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var PassHolderController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(PassHolderServiceInterface::class);
        $this->controller = new PassHolderController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_the_passholder_matching_a_provided_identification_number()
    {
        $identification = '0930000125607';

        $cardSystem = new \CultureFeed_Uitpas_CardSystem(1, 'uitpas');
        $cardSystem->id = $identification;

        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->cardSystem = $cardSystem;

        $passholder = new \CultureFeed_Uitpas_Passholder();
        $passholder->name = 'Foo';
        $passholder->cardSystemSpecific[1] = $cardSystemSpecific;

        $this->service->expects($this->once())
            ->method('getByIdentificationNumber')
            ->with($identification)
            ->willReturn($passholder);

        $request = new Request(['identification' => $identification]);
        $response = $this->controller->getByIdentificationNumber($request);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_passholder_can_not_be_found_by_identification()
    {
        $identification = '0930000125607';

        $this->service->expects($this->once())
            ->method('getByIdentificationNumber')
            ->with($identification)
            ->willReturn(null);

        $request = new Request(['identification' => $identification]);

        try {
            $this->controller->getByIdentificationNumber($request);
            $this->fail('getByIdentificationNumber() should throw PassHolderNotFoundException.');
        } catch (PassHolderNotFoundException $exception) {
            $this->assertInstanceOf(ReadableCodeExceptionInterface::class, $exception);
            $this->assertNotEmpty($exception->getReadableCode());
        }
    }

    /**
     * @test
     */
    public function it_responds_the_passholder_matching_a_provided_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $cardSystem = new \CultureFeed_Uitpas_CardSystem(1, 'uitpas');
        $cardSystem->id = $uitpasNumberValue;

        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->cardSystem = $cardSystem;

        $passholder = new \CultureFeed_Uitpas_Passholder();
        $passholder->name = 'Foo';
        $passholder->cardSystemSpecific[1] = $cardSystemSpecific;

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($passholder);

        $response = $this->controller->getByUitpasNumber($uitpasNumberValue);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder.json');
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
    public function it_updates_the_provided_fields_of_a_given_passholder_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $data = ['name' => 'Foo'];

        $request = new Request([], $data);

        $passHolder = new \CultureFeed_Uitpas_Passholder();
        $passHolder->name = $data['name'];

        $this->service->expects($this->once())
            ->method('update')
            ->with($uitpasNumber, $passHolder);

        $cardSystem = new \CultureFeed_Uitpas_CardSystem(1, 'uitpas');
        $cardSystem->id = $uitpasNumberValue;

        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->cardSystem = $cardSystem;

        $updated = new \CultureFeed_Uitpas_Passholder();
        $updated->name = 'Foo';
        $updated->cardSystemSpecific[1] = $cardSystemSpecific;

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($updated);

        $response = $this->controller->update($request, $uitpasNumberValue);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_passholder_can_not_be_updated()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $data = ['name' => 'Foo'];

        $request = new Request([], $data);

        $passHolder = new \CultureFeed_Uitpas_Passholder();
        $passHolder->name = $data['name'];

        $message = 'Something went wrong.';
        $code = 'SOMETHING_WRONG';

        $this->service->expects($this->once())
            ->method('update')
            ->with($uitpasNumber, $passHolder)
            ->willThrowException(new \CultureFeed_Exception($message, $code));

        $this->setExpectedException(ReadableCodeResponseException::class);
        $this->controller->update($request, $uitpasNumberValue);
    }
}
