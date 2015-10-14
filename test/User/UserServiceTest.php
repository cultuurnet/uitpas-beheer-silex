<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\User\UserNotFoundException;
use CultuurNet\UiTPASBeheer\User\UserService;
use ValueObjects\Web\EmailAddress;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ICultureFeed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cultureFeed;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var UserService
     */
    protected $service;

    public function setUp()
    {
        $this->cultureFeed = $this->getMock(\ICultureFeed::class);
        $this->counterConsumerKey = new CounterConsumerKey('abc');

        $this->service = new UserService(
            $this->cultureFeed,
            $this->counterConsumerKey
        );
    }

    /**
     * @test
     */
    public function it_can_return_a_user_for_a_specific_email_address()
    {
        $email = new EmailAddress('foo@bar.com');

        $expectedQuery = new \CultureFeed_SearchUsersQuery();
        $expectedQuery->mbox = $email->toNative();
        $expectedQuery->mboxIncludePrivate = true;

        $cfUser1 = new \CultureFeed_SearchUser();
        $cfUser1->id = '1';
        $cfUser1->nick = 'user1';

        $cfUser2 = new \CultureFeed_SearchUser();
        $cfUser2->id = '2';
        $cfUser2->nick = 'user2';

        $cfResults = new \CultureFeed_ResultSet();
        $cfResults->total = 2;
        $cfResults->objects[] = $cfUser1;
        $cfResults->objects[] = $cfUser2;

        $this->cultureFeed->expects($this->once())
            ->method('searchUsers')
            ->with($expectedQuery)
            ->willReturn($cfResults);

        $actual = $this->service->getUserByEmail($email);

        $expected = new User();
        $expected->id = '1';
        $expected->nick = 'user1';

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_no_user_is_found_for_a_specific_email_address()
    {
        $email = new EmailAddress('foo@bar.com');

        $cfResults = new \CultureFeed_ResultSet();
        $cfResults->total = 0;
        $cfResults->objects = [];

        $this->cultureFeed->expects($this->once())
            ->method('searchUsers')
            ->willReturn($cfResults);

        $this->setExpectedException(
            UserNotFoundException::class,
            'No user found with email "foo@bar.com".'
        );

        $this->service->getUserByEmail($email);
    }
}
