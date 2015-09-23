<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use DateTime;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class KansenStatuutEndDateJSONDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     * @return Date
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->endDate)) {
            throw new MissingPropertyException('endDate');
        }

        $dateTime = DateTime::createFromFormat('Y-m-d', $data->endDate);
        if (false === $dateTime) {
            throw new KansenStatuutEndDateInvalidException($data->endDate);
        }

        return Date::fromNativeDateTime($dateTime);
    }
}
