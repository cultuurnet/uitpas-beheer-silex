<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use ValueObjects\StringLiteral\StringLiteral;

class MemberServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var MemberService
     */
    protected $service;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('abc');

        $this->service = new MemberService(
            $this->uitpas,
            $this->counterConsumerKey
        );
    }

    /**
     * @test
     */
    public function it_can_return_a_list_of_all_members_for_the_active_counter()
    {
        $cfMemberAdmin = new \CultureFeed_Uitpas_Counter_Member();
        $cfMemberAdmin->id = '1af67b95-6f68-4a82-96af-7539681c28ca';
        $cfMemberAdmin->nick = 'john.doe';

        $cfMember = new \CultureFeed_Uitpas_Counter_Member();
        $cfMember->id = 'd6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb';
        $cfMember->nick = 'foo.bar';

        $cfMembers['admins'][] = $cfMemberAdmin;
        $cfMembers['members'][] = $cfMember;

        $this->uitpas->expects($this->once())
            ->method('getMembersForCounter')
            ->with($this->counterConsumerKey->toNative())
            ->willReturn($cfMembers);

        $actual = $this->service->all();

        $expected = [
            new Member(
                new Uid('1af67b95-6f68-4a82-96af-7539681c28ca'),
                new StringLiteral('john.doe'),
                MemberRole::ADMIN()
            ),
            new Member(
                new Uid('d6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb'),
                new StringLiteral('foo.bar'),
                MemberRole::MEMBER()
            ),
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_add_a_new_member_to_the_active_counter()
    {
        $uid = new Uid('d6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb');

        $this->uitpas->expects($this->once())
            ->method('addMemberToCounter')
            ->with(
                $uid->toNative(),
                $this->counterConsumerKey->toNative()
            );

        $this->service->add($uid);
    }

    /**
     * @test
     */
    public function it_can_remove_a_member_from_the_active_counter()
    {
        $uid = new Uid('d6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb');

        $this->uitpas->expects($this->once())
            ->method('removeMemberFromCounter')
            ->with(
                $uid->toNative(),
                $this->counterConsumerKey->toNative()
            );

        $this->service->remove($uid);
    }
}
