<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Properties;

use CultuurNet\Clock\Clock;
use CultuurNet\Clock\SystemClock;
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
     * @var Clock
     */
    protected $clock;

    /**
     * @param Age|null $from
     * @param Age|null $to
     */
    public function __construct(Age $from = null, Age $to = null)
    {
        $this->guardValidRange($from, $to);
        $this->from = $from;
        $this->to = $to;
        $this->clock = new SystemClock(new \DateTimeZone('Europe/Brussels'));
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
     * @param Clock $clock
     */
    public function overclock(Clock $clock)
    {
        $this->clock = $clock;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $jsonData = [];

        $currentDate = $this->clock->getDateTime();

        if ($from = $this->getFrom()) {
            $years = $from->toNative();
            $date = $currentDate->sub(new \DateInterval("P" . $years . "Y"));
            $jsonData['from'] = [
                "age" => $years,
                "date" => $date->format('c'),
            ];
        }

        if ($to = $this->getTo()) {
            $years = $to->toNative();
            $date = $currentDate->sub(new \DateInterval("P" . $years . "Y"));
            $jsonData['to'] = [
                "age" => $years,
                "date" => $date->format('c'),
            ];
        }

        return $jsonData;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_AgeRange $uitpasAgeRange
     * @return AgeRange
     */
    public static function fromCultureFeedUitpasAgeRange(\CultureFeed_Uitpas_Passholder_AgeRange $uitpasAgeRange)
    {
        $from = $uitpasAgeRange->ageFrom ? Age::fromNative($uitpasAgeRange->ageFrom) : null;
        $to = $uitpasAgeRange->ageTo ? Age::fromNative($uitpasAgeRange->ageTo) : null;

        $ageRange = new static(
            $from,
            $to
        );

        return $ageRange;

    }
}
