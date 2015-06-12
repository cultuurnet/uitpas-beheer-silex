<?php

namespace CultuurNet\UiTPASBeheer\Response;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonErrorResponse extends JsonResponse
{
    /**
     * @param \Exception $exception
     * @param int $status
     * @param array $headers
     */
    public function __construct(\Exception $exception, $status = 400, $headers = array())
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

        parent::__construct($data, $status, $headers);
    }
}
