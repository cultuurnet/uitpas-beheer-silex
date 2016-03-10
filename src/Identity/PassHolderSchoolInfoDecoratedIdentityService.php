<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\PassHolder\PassHolder;
use CultuurNet\UiTPASBeheer\School\SchoolServiceInterface;

class PassHolderSchoolInfoDecoratedIdentityService implements IdentityServiceInterface
{
    /**
     * @var IdentityServiceInterface
     */
    protected $decoratee;

    /**
     * @var SchoolServiceInterface
     */
    protected $schools;

    /**
     * PassHolderSchoolInfoDecoratedIdentityService constructor.
     * @param IdentityServiceInterface $decoratee
     * @param SchoolServiceInterface $schools
     */
    public function __construct(IdentityServiceInterface $decoratee, SchoolServiceInterface $schools)
    {
        $this->decoratee = $decoratee;
        $this->schools = $schools;
    }

    /**
     * @inheritdoc
     */
    public function get($identification)
    {
        $identity = $this->decoratee->get($identification);

        if ($identity && $identity->getPassHolder() instanceof PassHolder) {
            $passHolder = $identity->getPassHolder();
            $school = $passHolder->getSchool();
            if ($school) {
                $enhancedSchool = $this->schools
                    ->get($school->getId());

                $identity = $identity->withPassHolder(
                    $passHolder->withSchool($enhancedSchool)
                );
            }
        }

        return $identity;
    }
}
