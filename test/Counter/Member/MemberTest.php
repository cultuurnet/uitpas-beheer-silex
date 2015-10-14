<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use ValueObjects\StringLiteral\StringLiteral;

class MemberTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Uid
     */
    protected $uid;

    /**
     * @var StringLiteral
     */
    protected $nick;

    /**
     * @var MemberRole
     */
    protected $role;

    /**
     * @var Member
     */
    protected $member;

    public function setUp()
    {
        $this->uid = new Uid('1af67b95-6f68-4a82-96af-7539681c28ca');
        $this->nick = new StringLiteral('john.doe');
        $this->role = MemberRole::ADMIN();

        $this->member = new Member(
            $this->uid,
            $this->nick,
            $this->role
        );
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $this->assertEquals(
            $this->uid,
            $this->member->getUid()
        );

        $this->assertEquals(
            $this->nick,
            $this->member->getNick()
        );

        $this->assertEquals(
            $this->role,
            $this->member->getRole()
        );
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->member);
        $this->assertJsonEquals($json, 'Counter/Member/data/member.json');
    }

    /**
     * @test
     */
    public function it_can_be_initialized_from_a_culturefeed_counter_member_and_role()
    {
        $cfCounterMember = new \CultureFeed_Uitpas_Counter_Member();
        $cfCounterMember->id = '1af67b95-6f68-4a82-96af-7539681c28ca';
        $cfCounterMember->nick = 'john.doe';

        $actual = Member::fromCultureFeedCounterMember(
            $cfCounterMember,
            $this->role
        );

        $this->assertEquals($this->member, $actual);
    }
}
