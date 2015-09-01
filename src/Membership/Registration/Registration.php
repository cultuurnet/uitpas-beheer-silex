<?php

namespace CultuurNet\UiTPASBeheer\Membership\Registration;

use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use ValueObjects\DateTime\Date;

class Registration
{
    /**
     * @var AssociationId
     */
    protected $associationId;

    /**
     * @var Date|null
     */
    protected $endDate;

    /**
     * @param AssociationId $associationId
     */
    public function __construct(AssociationId $associationId)
    {
        $this->associationId = $associationId;
    }

    /**
     * @return AssociationId
     */
    public function getAssociationId()
    {
        return $this->associationId;
    }

    /**
     * @param Date $endDate
     * @return Registration
     */
    public function withEndDate(Date $endDate)
    {
        $c = clone $this;
        $c->endDate = $endDate;
        return $c;
    }

    /**
     * @return Date|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
