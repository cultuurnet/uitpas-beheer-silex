<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

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
