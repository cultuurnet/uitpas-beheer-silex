<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Security;

use CultuurNet\UiTIDProvider\Security\UiTIDToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RoleAddingAuthenticationProviderDecorator implements AuthenticationProviderInterface
{
    /**
     * @var AuthenticationProviderInterface
     */
    private $authenticator;

    /**
     * @var array
     */
    private $roles;

    /**
     * @param AuthenticationProviderInterface $authenticator
     * @param array $roles
     */
    public function __construct(AuthenticationProviderInterface $authenticator, array $roles)
    {
        $this->authenticator = $authenticator;
        $this->roles = $roles;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(TokenInterface $token)
    {
        $token = $this->authenticator->authenticate($token);

        if (!$token->isAuthenticated()) {
            return $token;
        }

        $userName = $token->getUserName();

        $roles = isset($this->roles[$userName]) ? $this->roles[$userName] : [];
        if (empty($roles)) {
            return $token;
        }

        $originalRoles = $token->getRoles();
        $roles = array_merge($roles, $originalRoles);
        $enhancedToken = new UiTIDToken($roles);
        $enhancedToken->setUser($token->getUser());

        return $enhancedToken;
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenInterface $token)
    {
        return $this->authenticator->supports($token);
    }
}
