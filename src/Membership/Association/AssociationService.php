<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;

class AssociationService extends CounterAwareUitpasService implements AssociationServiceInterface
{
    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getAssociationsByPermission(Permission $permission, $permissionValue = true)
    {
        $associations = $this->getUitpasService()
            ->getAssociations(
                $this->getCounterConsumerKey(),
                $permission === Permission::READ() ? $permissionValue : null,
                $permission === Permission::REGISTER() ? $permissionValue : null
            )
            ->objects;

        return new AssociationCollection($associations);
    }
}
