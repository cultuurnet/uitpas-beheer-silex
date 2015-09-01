<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;

interface MembershipServiceInterface
{
    /**
     * @return AssociationCollection $associationCollection
     */
    public function getAssociations();
}
