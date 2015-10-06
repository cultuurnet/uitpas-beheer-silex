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
        $from = $from->toNativeDateTime()->getTimestamp();
        $to = $to->toNativeDateTime()->getTimestamp();
        if ($from > $to) {
            throw new \InvalidArgumentException('Start date should be before or equal to end date.');
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
