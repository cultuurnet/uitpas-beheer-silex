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
     * @param bool $permissionValue
     * @return AssociationCollection $associationCollection
     */
    public function getAssociationsByPermission(Permission $permission, $permissionValue = true);
}
