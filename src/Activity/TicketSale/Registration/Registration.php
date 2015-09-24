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
     * @var TariffId|null
     */
    protected $tariffId;

    /**
     * @var Natural|null
     */
    protected $amount;

    /**
     * @param StringLiteral $activityId
     * @param PriceClass $priceClass
     * @param TariffId $tariffId
     */
    public function __construct(
        StringLiteral $activityId,
        PriceClass $priceClass,
        TariffId $tariffId = null
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
     * @param TariffId $tariffId
     * @return Registration
     */
    public function withTariffId(TariffId $tariffId)
    {
        $c = clone $this;
        $c->tariffId = $tariffId;
        return $c;
    }

    /**
     * @return TariffId|null
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
