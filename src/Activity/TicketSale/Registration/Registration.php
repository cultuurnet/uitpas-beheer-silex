<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use ValueObjects\Number\Natural;
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
     * @var Natural|null
     */
    protected $amount;

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
     * @return StringLiteral|null
     */
    public function getTariffId()
    {
        return $this->tariffId;
    }

    /**
     * @param Natural $amount
     * @return Registration
     */
    public function withAmount(Natural $amount)
    {
        $c = clone $this;
        $c->amount = $amount;
        return $c;
    }

    /**
     * @return Natural|null
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
