<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;
use DateTime;

class BirthInformationJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     * @return BirthInformation
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->date)) {
            throw new MissingPropertyException('date');
        }

        $dateTime = DateTime::createFromFormat('Y-m-d', $data->date);
        if (false === $dateTime) {
            throw new BirthDateInvalidException($data->date);
        }

        $birthInformation = new BirthInformation(
            Date::fromNativeDateTime($dateTime)
        );

        if (isset($data->place)) {
            $birthInformation = $birthInformation->withPlace(
                new StringLiteral((string) $data->place)
            );
        }

        return $birthInformation;
    }
}
