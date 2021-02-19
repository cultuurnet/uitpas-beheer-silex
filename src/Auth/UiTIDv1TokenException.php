<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use CultuurNet\UiTPASBeheer\Exception\ResponseException;

final class UiTIDv1TokenException extends ResponseException
{
    public static function unauthorized(): self
    {
        return new self(
            'Could not exchange token with UiTID v1 because the auth0 token was not found by UiTID v1.',
            401
        );
    }

    public static function noPermission(): self
    {
        return new self(
            'Could not exchange token with UiTID v1 because the oauth consumer has no permission to do so.',
            403
        );
    }

    public static function unknown(int $code = null, string $reason = null): self
    {
        return new self(
            sprintf(
                'Could not exchange token with UiTID v1 for unknown reason (code: %s, reason: %s)',
                $code,
                $reason
            ),
            $code ?? 500
        );
    }

    public static function invalidXmlResponse(string $details): self
    {
        return new self(
            'Could not exchange token with UiTID v1 because the response XML is invalid. ' . $details,
            500
        );
    }

    public static function failed(string $message): self
    {
        return new self(
            'Token exchange with UiTID v1 failed. Reason: ' . $message,
            500
        );
    }
}
