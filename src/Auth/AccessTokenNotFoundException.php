<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;

final class AccessTokenNotFoundException extends ResponseException
{
    public function __construct()
    {
        parent::__construct('No access token found.', Response::HTTP_NOT_FOUND);
    }
}
