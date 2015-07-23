<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\DateTime\DateTime;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinConstraint implements \JsonSerializable {

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
     * @var StringLiteral
     */
    protected $reason;

    /**
     * @param $allowed
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param StringLiteral $reason
     */
    public function __construct($allowed, DateTime $startDate = null, DateTime $endDate = null, StringLiteral $reason) {
        $this->allowed = $allowed;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reason = $reason;
    }

    /**
     * @return bool
     */
    public function getAllowed() {
        return $this->allowed;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @return StringLiteral
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() {
        return [
            'allowed' => $this->allowed,
            'startDate' => ($this->startDate) ? $this->startDate->toNativeDateTime()->getTimestamp() : null,
            'endDate' => ($this->endDate) ? $this->endDate->toNativeDateTime()->getTimestamp() : null,
            'reason' => $this->reason->toNative(),
        ];
    }
}
