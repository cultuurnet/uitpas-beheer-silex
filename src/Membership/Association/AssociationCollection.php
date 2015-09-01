<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

final class AssociationCollection implements \JsonSerializable
{
    /**
     * @var \CultureFeed_Uitpas_Association[]
     */
    protected $associations;

    /**
     * @param \CultureFeed_Uitpas_Association[] $cfAssociations
     */
    public function __construct(array $cfAssociations = array())
    {
        foreach ($cfAssociations as $cfAssociation) {
            $this->guardAssociationObjectType($cfAssociation);
            $this->associations[$cfAssociation->id] = $cfAssociation;
        }
    }

    /**
     * @param mixed $cfAssociation
     */
    private function guardAssociationObjectType($cfAssociation)
    {
        if (!($cfAssociation instanceof \CultureFeed_Uitpas_Association)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected instance of CultureFeed_Uitpas_Association, got %s instead.',
                    get_class($cfAssociation)
                )
            );
        }
    }

    /**
     * @param \CultureFeed_Uitpas_Association $cfAssociation
     * @return AssociationCollection
     */
    public function withAssociation(\CultureFeed_Uitpas_Association $cfAssociation)
    {
        $c = clone $this;
        $c->associations[$cfAssociation->id] = $cfAssociation;
        return $c;
    }

    /**
     * @param \CultureFeed_Uitpas_Association $cfAssociation
     * @return AssociationCollection
     */
    public function withoutAssociation(\CultureFeed_Uitpas_Association $cfAssociation)
    {
        $c = clone $this;
        unset($c->associations[$cfAssociation->id]);
        return $c;
    }

    /**
     * @return \CultureFeed_Uitpas_Association
     */
    public function getAssociationMap()
    {
        return $this->associations;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAssociationMap();
    }
}
