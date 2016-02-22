<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class CounterIDJsonDeserializer extends JSONDeserializer
{
    /**
     * @inheritdoc
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->id)) {
            throw new MissingPropertyException('id');
        }

        if (!is_string($data->id) || !is_numeric($data->id)) {
            throw new IncorrectParameterValueException('id');
        }

        return (string) $data['id'];
    }
}
