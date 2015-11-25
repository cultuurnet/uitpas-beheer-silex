<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;

class FeedbackNotSentException extends CompleteResponseException
{
    public function __construct($previous = null)
    {
        $message = 'An unknown error occurred while sending the feedback.';
        $readableCode = 'FEEDBACK_NOT_SENT';
        $code = 500;
        parent::__construct($message, $readableCode, $code, $previous);
    }
}
