<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\LegacyPassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications\HasAtLeastOneExpiredKansenStatuut;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationServiceInterface;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\Registration\Registration;
use CultuurNet\UiTPASBeheer\Membership\Registration\RegistrationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderNotFoundException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;

class MembershipControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var MembershipServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $membershipService;

    /**
     * @var LegacyPassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $legacyPassHolderService;

    /**
     * @var DeserializerInterface
     */
    protected $registrationJsonDeserializer;

    /**
     * @var HasAtLeastOneExpiredKansenStatuut
     */
    protected $hasAtLeastOneExpiredKansenStatuut;

    /**
     * @var AssociationServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $associationService;

    /**
     * @var MembershipController
     */
    protected $controller;

    public function setUp()
    {
        $this->membershipService = $this->getMock(MembershipServiceInterface::class);
        $this->legacyPassHolderService = $this->getMock(LegacyPassHolderServiceInterface::class);
        $this->registrationJsonDeserializer = new RegistrationJsonDeserializer();
        $this->hasAtLeastOneExpiredKansenStatuut = new HasAtLeastOneExpiredKansenStatuut();
        $this->associationService = $this->getMock(AssociationServiceInterface::class);

        $this->controller = new MembershipController(
            $this->membershipService,
            $this->registrationJsonDeserializer,
            $this->legacyPassHolderService,
            $this->hasAtLeastOneExpiredKansenStatuut,
            $this->associationService
        );
    }

    /**
     * @test
     */
    public function it_can_respond_all_associations_for_the_active_counter()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');

        $associationA = new \CultureFeed_Uitpas_Association();
        $associationA->id = 5;
        $associationA->name = 'Foo';

        $associationB = new \CultureFeed_Uitpas_Association();
        $associationB->id = 3;
        $associationB->name = 'Bar';

        $associationC = new \CultureFeed_Uitpas_Association();
        $associationC->id = 11;
        $associationC->name = 'Lorem';

        $all = new AssociationCollection(
            array(
                $associationA,
                $associationB,
                $associationC,
            )
        );

        $this->associationService->expects($this->once())
            ->method('getAssociations')
            ->willReturn($all);

        $membershipA = new \CultureFeed_Uitpas_Passholder_Membership();
        $membershipA->association = $associationA;
        $membershipA->id = 111;

        $membershipB = new \CultureFeed_Uitpas_Passholder_Membership();
        $membershipB->association = $associationB;
        $membershipB->id = 113;

        $cardSystem = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystem->kansenStatuut = true;
        $cardSystem->kansenStatuutExpired = true;
        $cardSystem->kansenStatuutEndDate = 1441097191;
        $cardSystem->kansenStatuutInGracePeriod = false;
        $cardSystem->kansenStatuutSuspendedUntil = 1441097191;

        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->memberships = array(
            $membershipA,
            $membershipB,
        );
        $cfPassHolder->cardSystemSpecific[] = $cardSystem;

        $this->legacyPassHolderService->expects($this->once())
            ->method('getByUiTPASNumber')
            ->with($uitpasNumber)
            ->willReturn($cfPassHolder);

        $response = $this->controller->listing($uitpasNumber->toNative());
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'Membership/data/profile.json');
    }

    /**
     * @test
     */
    public function it_can_register_a_membership()
    {
        $registrationJson = '{"associationId": "10"}';

        $request = new Request([], [], [], [], [], [], $registrationJson);
        $uitpasNumber = new UiTPASNumber('0930000420206');

        $registration = new Registration(
            new AssociationId('10')
        );

        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->uitIdUser = new \CultureFeed_Uitpas_Passholder_UitIdUser();
        $cfPassHolder->uitIdUser->id = '1000';

        $this->legacyPassHolderService->expects($this->once())
            ->method('getByUiTPASNumber')
            ->with($uitpasNumber)
            ->willReturn($cfPassHolder);

        $result = new \CultureFeed_Uitpas_Response();
        $result->code = 'SUCCESS';
        $result->message = 'Membership registered successfully.';

        $this->membershipService->expects($this->once())
            ->method('register')
            ->with(
                new StringLiteral('1000'),
                $registration
            )
            ->willReturn($result);

        $response = $this->controller->register(
            $request,
            $uitpasNumber->toNative()
        );

        $json = $response->getContent();
        $this->assertJsonEquals($json, 'Membership/data/registration-success.json');
    }

    /**
     * @test
     */
    public function it_can_stop_an_active_membership()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');
        $associationId = new AssociationId('123456789');

        $uid = new StringLiteral('1000');

        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->uitIdUser = new \CultureFeed_Uitpas_Passholder_UitIdUser();
        $cfPassHolder->uitIdUser->id = $uid->toNative();

        $this->legacyPassHolderService->expects($this->once())
            ->method('getByUiTPASNumber')
            ->with($uitpasNumber)
            ->willReturn($cfPassHolder);

        $result = new \CultureFeed_Uitpas_Response();
        $result->code = 'SUCCESS';
        $result->message = 'Membership stopped successfully.';

        $this->membershipService->expects($this->once())
            ->method('stop')
            ->with(
                $uid,
                $associationId
            )
            ->willReturn($result);

        $response = $this->controller->stop(
            $uitpasNumber->toNative(),
            $associationId->toNative()
        );

        $json = $response->getContent();
        $this->assertJsonEquals($json, 'Membership/data/stop-success.json');
    }

    /**
     * @test
     * @dataProvider passHolderNotFoundDataProvider
     *
     * @param string $method
     * @param array $arguments
     */
    public function it_throws_an_exception_when_no_passholder_can_be_found_for_an_uitpas_number(
        $method,
        $arguments
    ) {
        $this->legacyPassHolderService->expects($this->once())
            ->method('getByUiTPASNumber')
            ->willReturn(null);

        $this->setExpectedException(PassHolderNotFoundException::class);

        call_user_func_array(
            array(
                $this->controller,
                $method,
            ),
            $arguments
        );
    }

    /**
     * @return array
     */
    public function passHolderNotFoundDataProvider()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');

        return [
            [
                'listing',
                [
                    $uitpasNumber->toNative(),
                ],
            ],
            [
                'register',
                [
                    new Request(),
                    $uitpasNumber->toNative(),
                ],
            ],
            [
                'stop',
                [
                    $uitpasNumber->toNative(),
                    new AssociationId('134568795'),
                ],
            ],
        ];
    }
}
