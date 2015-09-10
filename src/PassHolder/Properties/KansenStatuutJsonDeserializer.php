<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;
use DateTime;

class KansenStatuutJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     * @return KansenStatuut
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

        $kansenStatuut = new KansenStatuut(
            Date::fromNativeDateTime($dateTime)
        );

        if (isset($data->remarks)) {
            $remarks = new Remarks($data->remarks);
            $kansenStatuut = $kansenStatuut->withRemarks($remarks);
        }

        return $kansenStatuut;
    }
}
