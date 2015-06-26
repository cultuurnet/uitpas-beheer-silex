<?php

namespace CultuurNet\UiTPASBeheer\Response;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonErrorResponse extends JsonResponse
{
    /**
     * @param ResponseException $exception
     * @param int $status
     * @param array $headers
     */
    public function __construct(ResponseException $exception)
    {
        $data = [
            'type' => 'error',
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        if ($exception instanceof ReadableCodeExceptionInterface) {
            $data['code'] = $exception->getReadableCode();
        }

        parent::__construct($data, $exception->getStatusCode(), $exception->getHeaders());
    }
}
