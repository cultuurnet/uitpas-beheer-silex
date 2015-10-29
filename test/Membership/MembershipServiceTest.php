<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\Registration\Registration;
use DoctrineTest\InstantiatorTestAsset\XMLReaderAsset;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class MembershipServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var MembershipService
     */
    protected $service;

    /**
     * @var \CultureFeed_Response
     */
    protected $cfResponse;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('foo-bar');

        $this->service = new MembershipService(
            $this->uitpas,
            $this->counterConsumerKey
        );

        $this->cfResponse = new \CultureFeed_Uitpas_Response();
        $this->cfResponse->code = 'SUCCESS';
        $this->cfResponse->message = 'All is well. Carry on.';
    }

    /**
     * @test
     */
    public function it_can_register_a_new_membership()
    {
        $uid = new StringLiteral('5');
        $associationId = new AssociationId('7');
        $registration = new Registration($associationId);

        $cfMembership = new \CultureFeed_Uitpas_Passholder_Membership();
        $cfMembership->associationId = $associationId->toNative();
        $cfMembership->uid = $uid->toNative();
        $cfMembership->balieConsumerKey = $this->counterConsumerKey->toNative();

        $this->uitpas->expects($this->once())
            ->method('createMembershipForPassholder')
            ->with($cfMembership)
            ->willReturn($this->cfResponse);

        $actualResponse = $this->service->register($uid, $registration);

        $this->assertEquals($this->cfResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function it_can_register_a_new_membership_with_a_specific_end_date()
    {
        $uid = new StringLiteral('5');
        $associationId = new AssociationId('7');
        $endDate = new Date(
            new Year(2015),
            Month::getByName('SEPTEMBER'),
            new MonthDay(30)
        );

        $registration = (new Registration(
            $associationId
        ))->withEndDate($endDate);

        $cfMembership = new \CultureFeed_Uitpas_Passholder_Membership();
        $cfMembership->associationId = $associationId->toNative();
        $cfMembership->uid = $uid->toNative();
        $cfMembership->balieConsumerKey = $this->counterConsumerKey->toNative();
        $cfMembership->endDate = 1443571200;

        $this->uitpas->expects($this->once())
            ->method('createMembershipForPassholder')
            ->with($cfMembership)
            ->willReturn($this->cfResponse);

        $actualResponse = $this->service->register($uid, $registration);

        $this->assertEquals($this->cfResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function it_can_stop_an_active_membership()
    {
        $uid = new StringLiteral('5');
        $associationId = new AssociationId('7');

        $this->uitpas->expects($this->once())
            ->method('deleteMembership')
            ->with(
                $uid->toNative(),
                $associationId->toNative(),
                $this->counterConsumerKey->toNative()
            )
            ->willReturn($this->cfResponse);

        $actualResponse = $this->service->stop($uid, $associationId);

        $this->assertEquals($this->cfResponse, $actualResponse);
    }
}
