<?php

namespace CultuurNet\UiTPASBeheer\DataValidation\Result;

use CultuurNet\UiTPASBeheer\DataValidation\Item\RealtimeValidationResult;
use Guzzle\Http\Message\Response;

/**
 * Response handler for real-time testing a single email address.
 */
class GetRealtimeValidationResult implements ResponseToResultInterface
{
    /**
     * @inheritdoc
     *
     * @return RealtimeValidationResult
     */
    public static function parseToResult(Response $response)
    {
        $data = json_decode($response->getBody(), true);

        $result = new RealtimeValidationResult();
        $result->setStatus(!empty($data['status']) ? $data['status'] : null);
        $result->setGrade(!empty($data['grade']) ? $data['grade'] : null);
        $result->setReason(!empty($data['reason']) ? $data['reason'] : null);

        return $result;
    }
}
