<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use ValueObjects\Number\Integer;

class PeriodConstraint implements \JsonSerializable
{
    /**
     * @var PeriodType
     */
    protected $type;

    /**
     * @var Integer
     */
    protected $volume;

    /**
     * PeriodConstraint constructor.
     *
     * @param PeriodType $type
     * @param \ValueObjects\Number\Integer $volume
     */
    public function __construct(PeriodType $type, Integer $volume)
    {
        $this->type = $type;
        $this->volume = $volume;
    }

    /**
     * @param \CultureFeed_Uitpas_PeriodConstraint $remainingTotal
     * @return PeriodConstraint
     */
    public static function fromCulturefeedPeriodConstraint(\CultureFeed_Uitpas_PeriodConstraint $remainingTotal)
    {
        $type = PeriodType::fromNative($remainingTotal->type);
        $volume = new Integer($remainingTotal->volume);

        return new PeriodConstraint($type, $volume);
    }

    /**
     * @return PeriodType
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
