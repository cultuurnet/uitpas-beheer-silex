<?php

namespace CultuurNet\UiTPASBeheer\DataValidation\Result;

use CultuurNet\UiTPASBeheer\DataValidation\Item\EmailValidationResult;
use Guzzle\Http\Message\Response;

/**
 * Response handler for real-time testing a single email address.
 */
class GetEmailValidationResult implements ResponseToResultInterface
{
    /**
     * @inheritdoc
     *
     * @return EmailValidationResult
     */
    public static function parseToResult(Response $response)
    {
        $data = json_decode($response->getBody(), true);

        $result = new EmailValidationResult();
        $result->setStatus(!empty($data['status']) ? $data['status'] : null);
        $result->setGrade(!empty($data['grade']) ? $data['grade'] : null);
        $result->setReason(!empty($data['reason']) ? $data['reason'] : null);

        return $result;
    }
}
