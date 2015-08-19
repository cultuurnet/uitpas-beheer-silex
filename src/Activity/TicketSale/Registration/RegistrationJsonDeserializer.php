<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     *
     * @return Registration
     *
     * @throws MissingPropertyException
     *   When a required property is missing.
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->activityId)) {
            throw new MissingPropertyException('activityId');
        }

        $activityId = new StringLiteral($data->activityId);
        $tariffId = isset($data->tariffId) ? new StringLiteral($data->tariffId) : null;

        return new Registration(
            $activityId,
            $tariffId
        );
    }
}
