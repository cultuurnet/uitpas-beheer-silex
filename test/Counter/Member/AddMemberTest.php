<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use ValueObjects\Web\EmailAddress;

class AddMemberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailAddress
     */
    protected $email;

    /**
     * @var AddMember
     */
    protected $addMember;

    public function setUp()
    {
        $this->email = new EmailAddress('foo@bar.com');
        $this->addMember = new AddMember($this->email);
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $this->assertEquals(
            $this->email,
            $this->addMember->getEmailAddress()
        );
    }
}
