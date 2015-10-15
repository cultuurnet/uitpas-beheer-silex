<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use ValueObjects\DateTime\Date;

class InvalidDateRangeException extends CompleteResponseException
{
    public function __construct(Date $from, Date $to)
    {
        $formattedRange =
            $from->toNativeDateTime()->format('Y-m-d') . ' - ' .
            $to->toNativeDateTime()->format('Y-m-d');
        $message = "Invalid date range {$formattedRange}. Start date should not be later than end date.";
        parent::__construct($message, 'INVALID_DATE_RANGE');
    }
}
