<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class ConnectToActivityJSONDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!property_exists($data, 'activityId')) {
            throw new MissingPropertyException('activityId');
        }

        if (null === $data->activityId) {
            return null;
        }

        return new StringLiteral($data->activityId);
    }
}
