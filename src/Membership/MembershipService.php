<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\Registration\Registration;
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

    /**
     * @param StringLiteral $uid
     * @param AssociationId $associationId
     *
     * @return \CultureFeed_Uitpas_Response
     */
    public function stop(StringLiteral $uid, AssociationId $associationId)
    {
        return $this->getUitpasService()->deleteMembership(
            $uid->toNative(),
            $associationId->toNative(),
            $this->getCounterConsumerKey()
        );
    }
}
