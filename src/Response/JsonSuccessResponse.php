<?php

namespace CultuurNet\UiTPASBeheer\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonSuccessResponse extends JsonResponse
{
    /**
     * @param string $message
     * @param int $status
     * @param array $headers
     */
    public function __construct($message = '', $status = 200, $headers = array())
    {
        $data = [
            'type' => 'success',
            'message' => $message,
        ];

        parent::__construct($data, $status, $headers);
    }
}
