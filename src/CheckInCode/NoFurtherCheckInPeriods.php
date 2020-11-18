<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\UiTPASBeheer\Exception\ResponseException;

final class NoFurtherCheckInPeriods extends ResponseException
{
    /**
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($code = 404, \Exception $previous = null)
    {
        $message = 'No further checkin periods found for the given activity.';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'NO_FUTURE_CHECKIN_PERIODS';
    }
}
