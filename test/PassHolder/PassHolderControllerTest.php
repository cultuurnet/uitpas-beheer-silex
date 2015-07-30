<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

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

    /**
     * @var PassHolder
     */
    protected $passHolder;

    public function setUp()
    {
        $this->service = $this->getMock(PassHolderServiceInterface::class);
        $this->controller = new PassHolderController($this->service);

        $name = new Name(
            new StringLiteral('Layla'),
            new StringLiteral('Zyrani')
        );

        $address = new Address(
            new StringLiteral('1090'),
            new StringLiteral('Jette (Brussel)')
        );

        $birthDate = new \DateTime('1976-08-13');
        $birthInformation = new BirthInformation(
            Date::fromNativeDateTime($birthDate)
        );

        $this->passHolder = new PassHolder($name, $address, $birthInformation);
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
            ->willReturn($this->passHolder);

        $response = $this->controller->getByUitpasNumber($uitpasNumberValue);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder-minimum.json');
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

        $this->service->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($this->passHolder);

        $response = $this->controller->update($request, $uitpasNumberValue);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder-minimum.json');
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
