<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use CultuurNet\UiTIDProvider\User\User;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\User\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class MemberControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var MemberServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $memberService;

    /**
     * @var UserServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    /**
     * @var AddMemberJsonDeserializer
     */
    protected $addMemberJsonDeserializer;

    /**
     * @var MemberController
     */
    protected $controller;

    /**
     * @var Member
     */
    protected $admin;

    /**
     * @var Member
     */
    protected $member;

    /**
     * @var Member[]
     */
    protected $members;

    public function setUp()
    {
        $this->memberService = $this->getMock(MemberServiceInterface::class);
        $this->userService = $this->getMock(UserServiceInterface::class);
        $this->addMemberJsonDeserializer = new AddMemberJsonDeserializer();

        $this->controller = new MemberController(
            $this->memberService,
            $this->userService,
            $this->addMemberJsonDeserializer
        );

        $this->admin = new Member(
            new Uid('1af67b95-6f68-4a82-96af-7539681c28ca'),
            new StringLiteral('john.doe'),
            MemberRole::ADMIN()
        );

        $this->member = new Member(
            new Uid('d6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb'),
            new StringLiteral('foo.bar'),
            MemberRole::MEMBER()
        );

        $this->members = [
            $this->admin,
            $this->member,
        ];
    }

    /**
     * @test
     */
    public function it_responds_all_members_of_the_active_counter()
    {
        $this->memberService->expects($this->once())
            ->method('all')
            ->willReturn($this->members);

        $response = $this->controller->all();
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'Counter/Member/data/members.json');
    }

    /**
     * @test
     */
    public function it_responds_the_uid_after_adding_a_new_member_to_the_active_counter()
    {
        $json = '{"email": "foo@bar.com"}';
        $request = new Request([], [], [], [], [], [], $json);

        $cfUser = new User();
        $cfUser->id = 'd6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb';
        $cfUser->nick = 'foo.bar';

        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with(new EmailAddress('foo@bar.com'))
            ->willReturn($cfUser);

        $this->memberService->expects($this->once())
            ->method('add')
            ->with(new Uid('d6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb'));

        $response = $this->controller->add($request);

        $json = $response->getContent();
        $uid = json_decode($json);

        $this->assertEquals($cfUser->id, $uid);
    }

    /**
     * @test
     */
    public function it_responds_after_removing_a_member_from_the_active_counter()
    {
        $uid = new Uid('d6ec5bbf-ff7c-4ae9-a7c1-f62df05c12fb');

        $this->memberService->expects($this->once())
            ->method('remove')
            ->with($uid);

        $response = $this->controller->remove(
            $uid->toNative()
        );

        $this->assertEquals(
            200,
            $response->getStatusCode()
        );

        $this->assertEmpty(
            $response->getContent()
        );
    }
}
