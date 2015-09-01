<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

class UnregisteredAssociationFilter implements AssociationFilterInterface
{
    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $cfPassHolder;

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     */
    public function __construct(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        $this->cfPassHolder = $cfPassHolder;
    }

    /**
     * @param AssociationCollection $associationCollection
     * @return AssociationCollection
     */
    public function filter(AssociationCollection $associationCollection)
    {
        // Remove associations that have a corresponding membership for the
        // passholder.
        foreach ($this->cfPassHolder->memberships as $membership) {
            $associationCollection = $associationCollection->withoutAssociation(
                $membership->association
            );
        }
    }
}
