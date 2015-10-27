<?php

namespace CultuurNet\UiTPASBeheer\Counter\Association;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;

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
    public function getAssociationsByPermission(Permission $permission)
    {
        $associations = $this->getUitpasService()
            ->getAssociations(
                $this->getCounterConsumerKey(),
                $permission === Permission::READ() ? true : false,
                $permission === Permission::REGISTER() ? true : false
            )
            ->objects;

        return new AssociationCollection($associations);
    }
}
