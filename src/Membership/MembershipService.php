<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;

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
}
