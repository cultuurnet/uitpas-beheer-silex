<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class CounterNotSetException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @param \CultureFeed_User $user
     * @param int $code
     * @param null $previous
     */
    public function __construct(\CultureFeed_User $user, $code = 0, $previous = null)
    {
        $message = sprintf('No active counter set for user %s.', $user->nick);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public static function getReadableCode()
    {
        return 'COUNTER_NOT_SET';
    }
}
