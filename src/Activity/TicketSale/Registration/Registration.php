<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use ValueObjects\StringLiteral\StringLiteral;

final class Registration
{
    /**
     * @var StringLiteral
     */
    protected $activityId;

    /**
     * @var PriceClass
     */
    protected $priceClass;

    /**
     * @var StringLiteral|null
     */
    protected $tariffId;

    /**
     * @param StringLiteral $activityId
     * @param PriceClass $priceClass
     * @param StringLiteral $tariffId
     */
    public function __construct(
        StringLiteral $activityId,
        PriceClass $priceClass,
        StringLiteral $tariffId = null
    ) {
        $this->activityId = $activityId;
        $this->priceClass = $priceClass;
        $this->tariffId = $tariffId;
    }

    /**
     * @return StringLiteral
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @return PriceClass
     */
    public function getPriceClass()
    {
        return $this->priceClass;
    }

    /**
     * @param StringLiteral $tariffId
     * @return Registration
     */
    public function withTariffId(StringLiteral $tariffId)
    {
        $c = clone $this;
        $c->tariffId = $tariffId;
        return $c;
    }

    /**
     * @return StringLiteral
     */
    public function getTariffId()
    {
        return $this->tariffId;
    }
}
