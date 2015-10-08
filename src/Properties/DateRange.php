<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use ValueObjects\DateTime\Date;

class DateRange implements \JsonSerializable
{
    /**
     * @var Date
     */
    private $from;

    /**
     * @var Date
     */
    private $to;

    /**
     * @param Date $from
     * @param Date $to
     */
    public function __construct(Date $from, Date $to)
    {
        $this->guardValidRange($from, $to);

        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param Date $from
     * @param Date $to
     *
     * @throws \InvalidArgumentException
     *   When the from date is after the to date.
     */
    private function guardValidRange(Date $from, Date $to)
    {
        $fromTimestamp = $from->toNativeDateTime()->getTimestamp();
        $toTimestamp = $to->toNativeDateTime()->getTimestamp();
        if ($fromTimestamp > $toTimestamp) {
            throw new InvalidDateRangeException($from, $to);
        }
    }

    /**
     * @return Date
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return Date
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'from' => $this->from->toNativeDateTime()->format('Y-m-d'),
            'to' => $this->to->toNativeDateTime()->format('Y-m-d'),
        ];
    }
}
