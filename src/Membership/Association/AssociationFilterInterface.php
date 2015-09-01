<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

interface AssociationFilterInterface
{
    /**
     * @param AssociationCollection $associationCollection
     * @return AssociationCollection
     */
    public function filter(AssociationCollection $associationCollection);
}
