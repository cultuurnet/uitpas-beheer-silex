<?php

namespace CultuurNet\UiTPASBeheer\Advantage\CashIn;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class CashInJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $string
     *
     * @return CashIn
     *
     * @throws MissingPropertyException
     *   When the 'advantageId' property is missing.
     */
    public function deserialize(StringLiteral $string)
    {
        $data = parent::deserialize($string);

        if (!isset($data->advantageId)) {
            throw new MissingPropertyException('advantageId', 'MISSING_ADVANTAGE_ID_PROPERTY');
        }

        return new CashIn(
            new StringLiteral((string) $data->advantageId)
        );
    }
}
