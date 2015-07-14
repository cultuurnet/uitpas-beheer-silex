<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class AdvantageIdentifierJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $string
     *
     * @return AdvantageIdentifier
     *
     * @throws MissingPropertyException
     *   When the id property is missing from the JSON data.
     */
    public function deserialize(StringLiteral $string)
    {
        $data = parent::deserialize($string);

        if (!isset($data->id)) {
            throw new MissingPropertyException('id', 'MISSING_ADVANTAGE_ID');
        }

        return new AdvantageIdentifier($data->id);
    }
}
