<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\TariffType;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

final class Registration
{
    /**
     * @var StringLiteral
     */
    protected $activityId;

    /**
     * @var StringLiteral|null
     */
    protected $tariffId;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $amount;

    /**
     * @param StringLiteral $activityId
     * @param StringLiteral $tariffId
     */
    public function __construct(
        StringLiteral $activityId,
        StringLiteral $tariffId = null
    ) {
        $this->activityId = $activityId;
        $this->tariffId = $tariffId;
        $this->amount = new Integer(1);
    }

    /**
     * @param \ValueObjects\Number\Integer $amount
     * @return Registration
     */
    public function withAmount(Integer $amount)
    {
        $c = clone $this;
        $c->amount = $amount;
        return $c;
    }

    /**
     * @return StringLiteral
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @return StringLiteral
     */
    public function getTariffId()
    {
        return $this->tariffId;
    }

    /**
     * @return \ValueObjects\Number\Integer
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
