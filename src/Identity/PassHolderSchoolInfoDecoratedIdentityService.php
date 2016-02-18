<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Identity;

class PassHolderSchoolInfoDecoratedIdentityService implements IdentityServiceInterface
{
    /**
     * @var IdentityServiceInterface
     */
    protected $decoratee;

    /**
     * PassHolderSchoolInfoDecoratedIdentityService constructor.
     * @param IdentityServiceInterface $decoratee
     */
    public function __construct(IdentityServiceInterface $decoratee)
    {
        $this->decoratee = $decoratee;
    }

    /**
     * @inheritdoc
     */
    public function get($identification)
    {
        return $this->decoratee->get($identification);
    }
}
