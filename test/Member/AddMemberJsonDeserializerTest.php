<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class AddMemberJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddMemberJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new AddMemberJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_decodes_a_valid_add_member_command()
    {
        $json = '{"email": "foo@bar.com"}';

        $expected = new AddMember(
            new EmailAddress('foo@bar.com')
        );

        $actual = $this->deserializer->deserialize(
            new StringLiteral($json)
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_email_is_missing()
    {
        $json = '{}';

        $this->setExpectedException(MissingPropertyException::class);

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_email_is_invalid()
    {
        $json = '{"email": "foo"}';

        $this->setExpectedException(IncorrectParameterValueException::class);

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }
}
