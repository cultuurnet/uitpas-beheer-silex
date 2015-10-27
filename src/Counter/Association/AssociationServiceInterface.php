<?php

namespace CultuurNet\UiTPASBeheer\Counter\Association;

use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;

interface AssociationServiceInterface
{
    /**
     * @return AssociationCollection $associationCollection
     */
    public function getAssociations();

    /**
     * @param Permission $permission
     * @return AssociationCollection $associationCollection
     */
    public function getAssociationsByPermission(Permission $permission);
}
