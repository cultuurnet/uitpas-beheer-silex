<?php

namespace CultuurNet\UiTPASBeheer\DataValidation\Item;

/**
 * Class RealtimeValidationResult
 * Contains the real-time validation result.
 */
class RealtimeValidationResult
{
    const REALTIME_VALIDATION_RESULT_STATUS_OK = 'ok';
    const REALTIME_VALIDATION_RESULT_STATUS_ERROR = 'error';

    /**
     * @var string
     *  The status for the request (ok, error)
     */
    protected $status;

    /**
     * @var string
     *  The grade for the tested email address (A+, A, B, D...)
     */
    protected $grade;

    /**
     * @var string
     *  The reason for the error, if any
     */
    protected $reason;

    /**
     * Checks if the status returned ok.
     * If not, an error was encountered (eg. unauthorized, tokens exhausted,...)
     *
     * @return bool
     */
    public function isOK()
    {
        return $this->getStatus() === self::REALTIME_VALIDATION_RESULT_STATUS_OK;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return RealtimeValidationResult
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param string $grade
     * @return RealtimeValidationResult
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     * @return RealtimeValidationResult
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }
}
