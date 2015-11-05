<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Security;

use CultuurNet\UiTIDProvider\Security\UiTIDToken;
use CultuurNet\UiTIDProvider\User\User;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RoleAddingAuthenticationProviderDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthenticationProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $wrappedAuthenticator;

    /**
     * @var RoleAddingAuthenticationProviderDecorator
     */
    private $authenticator;

    public function setUp()
    {
        $roles = [
            'johndoe' => [
                'ROLE_EDITOR',
                'ROLE_SUPPORT',
            ],
            'janedoe' => [
                'ROLE_EDITOR',
            ],
        ];

        $this->wrappedAuthenticator = $this->getMock(
            AuthenticationProviderInterface::class
        );

        $this->authenticator = new RoleAddingAuthenticationProviderDecorator(
            $this->wrappedAuthenticator,
            $roles
        );
    }

    /**
     * @test
     */
    public function it_ignores_users_without_any_additional_roles()
    {
        $initialToken = new UiTIDToken();
        $initialToken->setUser('janjanssen');

        $user = new User();
        $user->nick = 'janjanssen';

        $authenticatedToken = new UiTIDToken($user->getRoles());
        $authenticatedToken->setUser($user);

        $this->wrappedAuthenticator->expects($this->once())
            ->method('authenticate')
            ->with($initialToken)
            ->willReturn($authenticatedToken);

        $token = $this->authenticator->authenticate($initialToken);

        $this->assertEquals(
            $authenticatedToken,
            $token
        );
    }

    /**
     * @test
     */
    public function it_ignores_unauthenticated_tokens()
    {
        $initialToken = new UiTIDToken();
        $initialToken->setUser('janjanssen');

        $this->wrappedAuthenticator->expects($this->once())
            ->method('authenticate')
            ->with($initialToken)
            ->willReturn($initialToken);

        $token = $this->authenticator->authenticate($initialToken);

        $this->assertEquals(
            $initialToken,
            $token
        );
    }

    public function additionalRolesProvider()
    {
        return [
            ['johndoe', ['ROLE_EDITOR', 'ROLE_SUPPORT', 'UITID_USER']],
            ['janedoe', ['ROLE_EDITOR', 'UITID_USER']],
        ];
    }

    /**
     * @test
     * @dataProvider additionalRolesProvider
     */
    public function it_adds_additional_roles($userName, $expectedRoles)
    {
        $initialToken = new UiTIDToken();
        $initialToken->setUser($userName);

        $user = new User();
        $user->nick = $userName;

        $authenticatedToken = new UiTIDToken($user->getRoles());
        $authenticatedToken->setUser($user);

        $this->wrappedAuthenticator->expects($this->once())
            ->method('authenticate')
            ->with($initialToken)
            ->willReturn($authenticatedToken);

        $token = $this->authenticator->authenticate($initialToken);

        $expectedToken = new UiTIDToken($expectedRoles);
        $expectedToken->setUser($user);

        $this->assertEquals(
            $expectedToken,
            $token
        );
    }

    /**
     * @test
     */
    public function it_supports_same_tokens_as_wrapped_authenticator()
    {
        $userNamePasswordToken = new UsernamePasswordToken(
            'johndoe',
            '***',
            'provider'
        );

        $this->wrappedAuthenticator->expects($this->exactly(2))
            ->method('supports')
            ->withConsecutive(
                [new UiTIDToken()],
                [$userNamePasswordToken]
            )
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $this->assertTrue(
            $this->authenticator->supports(new UiTIDToken())
        );
        $this->assertFalse(
            $this->authenticator->supports($userNamePasswordToken)
        );
    }
}
