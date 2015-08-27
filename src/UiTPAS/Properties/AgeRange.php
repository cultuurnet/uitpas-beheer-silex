<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Properties;

use ValueObjects\Person\Age;

class AgeRange implements \JsonSerializable
{
    /**
     * @var Age
     */
    protected $from;

    /**
     * @var Age
     */
    protected $to;

    /**
     * @param Age|null $from
     * @param Age|null $to
     */
    public function __construct(Age $from = null, Age $to = null)
    {
        $this->guardValidRange($from, $to);
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param Age|null $from
     * @param Age|null $to
     *
     * @throws InvalidAgeRangeException
     */
    private function guardValidRange(Age $from = null, Age $to = null)
    {
        // one of the range limits can be unspecified but not both
        if (is_null($from) && is_null($to)) {
            throw new InvalidAgeRangeException();
        };

        // make sure the range does not end before it starts
        if (!is_null($from) && !is_null($to)) {
            if ($from->toNative() >= $to->toNative()) {
                throw new InvalidAgeRangeException();
            }
        }
    }

    /**
     * @return Age|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return Age|null
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $jsonData = [];

        if ($from = $this->getFrom()) {
            $jsonData['from'] = $from->toNative();
        }

        if ($to = $this->getTo()) {
            $jsonData['to'] = $to->toNative();
        }

        return $jsonData;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_AgeRange $uitpasAgeRange
     * @return AgeRange
     */
    public static function fromCultureFeedUitpasAgeRange(\CultureFeed_Uitpas_Passholder_AgeRange $uitpasAgeRange)
    {
        /* @var Age|null $from */
        $from = $uitpasAgeRange->ageFrom ? Age::fromNative($uitpasAgeRange->ageFrom) : null;

        /* @var Age|null $to */
        $to = $uitpasAgeRange->ageTo ? Age::fromNative($uitpasAgeRange->ageTo) : null;

        $ageRange = new static(
            $from,
            $to
        );

        return $ageRange;

    }
}
