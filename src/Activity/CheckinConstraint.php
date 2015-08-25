<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\DateTime\DateTime;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckinConstraint implements \JsonSerializable
{

    /**
     * @var boolean
     */
    protected $allowed;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var StringLiteral|null
     */
    protected $reason;

    /**
     * @param bool $allowed
     * @param DateTime $startDate
     * @param DateTime $endDate
     */
    public function __construct($allowed, DateTime $startDate, DateTime $endDate)
    {
        $this->allowed = (bool) $allowed;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return bool
     */
    public function getAllowed()
    {
        return $this->allowed;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return StringLiteral|null
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $data = [
            'allowed' => $this->allowed,
            'startDate' => $this->startDate->toNativeDateTime()->format(\DateTime::RFC3339),
            'endDate' => $this->endDate->toNativeDateTime()->format(\DateTime::RFC3339),
        ];

        if (!is_null($this->reason)) {
            $data['reason'] = $this->reason->toNative();
        }

        return $data;
    }

    /**
     * @param StringLiteral $reason
     * @return CheckinConstraint
     */
    public function withReason(StringLiteral $reason)
    {
        $c = clone $this;
        $c->reason = $reason;
        return $c;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CultureEvent $event
     * @return CheckinConstraint
     */
    public static function fromCultureFeedUitpasEvent(\CultureFeed_Uitpas_Event_CultureEvent $event)
    {
        $checkinConstraint = new CheckinConstraint(
            $event->checkinAllowed,
            CheckinConstraint::dateTimeFromTimestamp($event->checkinStartDate),
            CheckinConstraint::dateTimeFromTimestamp($event->checkinEndDate)
        );

        if (!$event->checkinAllowed && $event->checkinConstraintReason) {
            $checkinConstraint = $checkinConstraint->withReason(
                new StringLiteral((string) $event->checkinConstraintReason)
            );
        }

        return $checkinConstraint;
    }

    /**
     * @param int $timestamp
     * @return DateTime
     */
    protected static function dateTimeFromTimestamp($timestamp)
    {
        $timestamp = (int) $timestamp;
        $native = new \DateTime('@' . $timestamp);
        return DateTime::fromNativeDateTime($native);
    }
}
