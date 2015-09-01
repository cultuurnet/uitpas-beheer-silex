<?php

namespace CultuurNet\UiTPASBeheer\Membership\Registration;

use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use ValueObjects\DateTime\DateTime;

class Registration
{
    /**
     * @var AssociationId
     */
    protected $associationId;

    /**
     * @var DateTime|null
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
     * @param DateTime $endDate
     * @return Registration
     */
    public function withEndDate(DateTime $endDate)
    {
        $c = clone $this;
        $c->endDate = $endDate;
        return $c;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
