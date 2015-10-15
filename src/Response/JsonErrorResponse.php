<?php

namespace CultuurNet\UiTPASBeheer\Response;

use CultuurNet\UiTPASBeheer\Exception\ContextualExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonErrorResponse extends JsonResponse
{
    /**
     * @param ResponseException $exception
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

        if ($exception instanceof ContextualExceptionInterface && !is_null($exception->getContext())) {
            $data['context'] = $exception->getContext();
        }

        parent::__construct($data, $exception->getStatusCode(), $exception->getHeaders());
    }
}
