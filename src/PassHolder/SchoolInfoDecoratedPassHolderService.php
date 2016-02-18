<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\School\SchoolServiceInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class SchoolInfoDecoratedPassHolderService extends PassHolderServiceInterfaceDecoraterBase
{
    /**
     * @var SchoolServiceInterface
     */
    protected $schools;

    /**
     * SchoolInfoDecoratedPassHolderService constructor.
     * @param PassHolderServiceInterface $decoratee
     * @param SchoolServiceInterface $schools
     */
    public function __construct(
        PassHolderServiceInterface $decoratee,
        SchoolServiceInterface $schools
    ) {
        parent::__construct($decoratee);

        $this->schools = $schools;
    }

    public function getByUitpasNumber(UiTPASNumber $uitpasNumber)
    {
        $passHolder = parent::getByUitpasNumber(
            $uitpasNumber
        );

        if ($passHolder) {
            $school = $passHolder->getSchool();
            if ($school) {
                $enhancedSchool = $this->schools
                    ->get($school->getId());

                $passHolder = $passHolder->withSchool($enhancedSchool);
            }
        }

        return $passHolder;
    }
}
