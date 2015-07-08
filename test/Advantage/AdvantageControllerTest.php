<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Exception\InternalErrorException;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class AdvantageControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var DeserializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $advantageIdentifierJsonDeserializer;

    /**
     * @var AdvantageController
     */
    protected $controller;

    /**
     * @var AdvantageServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $welcomeAdvantageService;

    /**
     * @var AdvantageServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pointsPromotionAdvantageService;

    public function setUp()
    {
        $this->advantageIdentifierJsonDeserializer = $this->getMock(DeserializerInterface::class);

        $this->welcomeAdvantageService = $this->getMock(AdvantageServiceInterface::class);
        $this->pointsPromotionAdvantageService = $this->getMock(AdvantageServiceInterface::class);

        $this->controller = new AdvantageController(
            $this->advantageIdentifierJsonDeserializer
        );

        $this->controller->registerAdvantageService(
            AdvantageType::WELCOME(),
            $this->welcomeAdvantageService
        );

        $this->controller->registerAdvantageService(
            AdvantageType::POINTS_PROMOTION(),
            $this->pointsPromotionAdvantageService
        );
    }

    /**
     * @test
     * @dataProvider advantageDataProvider
     *
     * @param Advantage $advantage
     * @param string $advantageFile
     * @param string $serviceVariableName
     */
    public function it_can_respond_a_specific_advantage_of_either_type_for_a_passholder(
        Advantage $advantage,
        $advantageFile,
        $serviceVariableName
    ) {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $identifier = $advantage->getIdentifier();

        /* @var AdvantageServiceInterface|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->{$serviceVariableName};
        $service->expects($this->once())
            ->method('get')
            ->with(
                $uitpasNumber,
                $identifier->getId()
            )
            ->willReturn($advantage);

        $response = $this->controller->get(
            $uitpasNumber->toNative(),
            $identifier->toNative()
        );
        $json = $response->getContent();

        $this->assertJsonEquals($json, $advantageFile);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_specific_advantage_could_not_be_found_for_a_passholder()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $identifier = new AdvantageIdentifier('welcome--10');

        $this->welcomeAdvantageService->expects($this->once())
            ->method('get')
            ->with(
                $uitpasNumber,
                $identifier->getId()
            )
            ->willReturn(null);

        $this->setExpectedException(AdvantageNotFoundException::class);

        $this->controller->get(
            $uitpasNumber->toNative(),
            $identifier->toNative()
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_no_service_is_registered_for_a_valid_advantage_type()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $identifier = new AdvantageIdentifier('welcome--10');

        $controller = new AdvantageController(
            $this->advantageIdentifierJsonDeserializer
        );

        $this->setExpectedException(InternalErrorException::class);

        $controller->get(
            $uitpasNumber->toNative(),
            $identifier->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_respond_a_list_of_exchangeable_advantages_of_all_types_for_a_passholder()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $welcomeAdvantage = new WelcomeAdvantage(
            new StringLiteral('10'),
            new StringLiteral('Some title'),
            true
        );

        $pointsPromotion = new PointsPromotionAdvantage(
            new StringLiteral('10'),
            new StringLiteral('Some title'),
            new Integer(5),
            true
        );

        $this->welcomeAdvantageService->expects($this->once())
            ->method('getExchangeable')
            ->with($uitpasNumber)
            ->willReturn([$welcomeAdvantage]);

        $this->pointsPromotionAdvantageService->expects($this->once())
            ->method('getExchangeable')
            ->with($uitpasNumber)
            ->willReturn([$pointsPromotion]);

        $response = $this->controller->getExchangeable($uitpasNumber->toNative());
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'Advantage/data/exchangeableAdvantages.json');
    }

    /**
     * @test
     * @dataProvider advantageDataProvider
     *
     * @param Advantage $advantage
     * @param string $advantageFile
     * @param string $serviceVariableName
     */
    public function it_can_exchange_an_advantage_for_a_passholder(
        Advantage $advantage,
        $advantageFile,
        $serviceVariableName
    ) {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $identifier = $advantage->getIdentifier();

        $postData = ['id' => $identifier];
        $jsonPostData = json_encode($postData);
        $request = new Request([], [], [], [], [], [], $jsonPostData);

        $this->advantageIdentifierJsonDeserializer->expects($this->once())
            ->method('deserialize')
            ->with($jsonPostData)
            ->willReturn($identifier);

        /* @var AdvantageServiceInterface|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->{$serviceVariableName};

        $service->expects($this->once())
            ->method('exchange')
            ->with(
                $uitpasNumber,
                $identifier->getId()
            );

        $service->expects($this->once())
            ->method('get')
            ->with(
                $uitpasNumber,
                $identifier->getId()
            )
            ->willReturn($advantage);

        $response = $this->controller->exchange(
            $request,
            $uitpasNumber->toNative()
        );
        $responseJson = $response->getContent();

        $this->assertJsonEquals($responseJson, $advantageFile);
    }

    /**
     * @test
     */
    public function it_throws_a_meaningful_exception_when_an_advantage_could_not_be_exchanged()
    {
        $advantage = new WelcomeAdvantage(
            new StringLiteral('10'),
            new StringLiteral('Some title'),
            true
        );

        $uitpasNumber = new UiTPASNumber('0930000125607');
        $identifier = $advantage->getIdentifier();

        $request = new Request();

        $this->advantageIdentifierJsonDeserializer->expects($this->once())
            ->method('deserialize')
            ->willReturn($identifier);

        $exceptionMessage = 'Exchange failed.';
        $exceptionCode = 'EXCHANGE_FAILED';

        $cfException = new \CultureFeed_Exception($exceptionMessage, $exceptionCode);

        $this->welcomeAdvantageService->expects($this->once())
            ->method('exchange')
            ->with(
                $uitpasNumber,
                $identifier->getId()
            )
            ->willThrowException($cfException);

        try {
            $this->controller->exchange(
                $request,
                $uitpasNumber->toNative()
            );
            $this->fail('AdvantageController::exchange should throw a ReadableCodeResponseException.');
        } catch (ReadableCodeResponseException $exception) {
            $this->assertEquals($exceptionCode, $exception->getReadableCode());
            $this->assertEquals($exceptionMessage, $exception->getMessage());
        } catch (\Exception $exception) {
            $this->fail('AdvantageController::exchange threw an unexpected exception class.');
        }
    }

    /**
     * @return array
     */
    public function advantageDataProvider()
    {
        return [
            [
                new WelcomeAdvantage(
                    new StringLiteral('10'),
                    new StringLiteral('Some title'),
                    true
                ),
                'Advantage/data/welcomeAdvantage.json',
                'welcomeAdvantageService',
            ],
            [
                new PointsPromotionAdvantage(
                    new StringLiteral('10'),
                    new StringLiteral('Some title'),
                    new Integer(5),
                    true
                ),
                'Advantage/data/pointsPromotionAdvantage.json',
                'pointsPromotionAdvantageService',
            ],
        ];
    }
}
