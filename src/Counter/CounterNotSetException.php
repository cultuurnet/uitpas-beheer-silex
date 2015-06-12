<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;

class CounterNotSetException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @param \CultureFeed_User $user
     * @param int $code
     * @param null $previous
     */
    public function __construct(\CultureFeed_User $user, $code = Response::HTTP_NOT_FOUND, $previous = null)
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
