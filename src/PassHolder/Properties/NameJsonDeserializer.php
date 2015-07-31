<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class NameJsonDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->first)) {
            throw new MissingPropertyException('first');
        }
        if (empty($data->last)) {
            throw new MissingPropertyException('last');
        }

        $name = new Name(
            new StringLiteral((string) $data->first),
            new StringLiteral((string) $data->last)
        );

        if (isset($data->middle)) {
            $name = $name->withMiddleName(
                new StringLiteral((string) $data->middle)
            );
        }

        return $name;
    }
}
