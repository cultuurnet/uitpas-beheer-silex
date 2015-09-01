<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\LegacyPassHolderService;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\LegacyPassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;
use CultuurNet\UiTPASBeheer\Membership\Registration\Registration;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class MembershipService extends CounterAwareUitpasService implements MembershipServiceInterface
{
    /**
     * @return AssociationCollection $associationCollection
     */
    public function getAssociations()
    {
        $associations = $this->getUitpasService()
            ->getAssociations(
                $this->getCounterConsumerKey()
            )
            ->objects;

        return new AssociationCollection($associations);
    }

    /**
     * @param StringLiteral $uid
     * @param Registration $registration
     *
     * @return \CultureFeed_Uitpas_Response
     */
    public function register(StringLiteral $uid, Registration $registration)
    {
        $membership = new \CultureFeed_Uitpas_Passholder_Membership();
        $membership->associationId = $registration->getAssociationId()->toNative();
        $membership->balieConsumerKey = $this->getCounterConsumerKey();
        $membership->uid = $uid->toNative();

        if (!is_null($registration->getEndDate())) {
            $membership->endDate = $registration->getEndDate()
                ->toNativeDateTime()
                ->getTimestamp();
        }

        return $this->getUitpasService()
            ->createMembershipForPassholder($membership);
    }
}
