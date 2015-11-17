<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use ValueObjects\Number\Integer;

class RemainingTotal implements \JsonSerializable
{
    /**
     * @var RemainingTotalType
     */
    protected $type;

    /**
     * @var Integer
     */
    protected $volume;

    /**
     * RemainingTotal constructor.
     *
     * @param RemainingTotalType $type
     * @param \ValueObjects\Number\Integer $volume
     */
    public function __construct(RemainingTotalType $type, Integer $volume)
    {
        $this->type = $type;
        $this->volume = $volume;
    }

    /**
     * @param \CultureFeed_Uitpas_PeriodConstraint $remainingTotal
     * @return RemainingTotal
     */
    public static function fromCulturefeedPeriodConstraint(\CultureFeed_Uitpas_PeriodConstraint $remainingTotal)
    {
        $type = RemainingTotalType::fromNative($remainingTotal->type);
        $volume = new Integer($remainingTotal->volume);

        return new RemainingTotal($type, $volume);
    }

    /**
     * @return RemainingTotalType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \ValueObjects\Number\Integer
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'period' => $this->getType()->toNative(),
            'volume' => $this->getVolume()->toNative(),
        ];
    }
}
