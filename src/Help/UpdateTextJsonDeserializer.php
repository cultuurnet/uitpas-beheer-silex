<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class UpdateTextJsonDeserializer extends JSONDeserializer
{
    /**
     * @inheritdoc
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->text)) {
            throw new MissingPropertyException('text');
        }

        return new Text($data->text);
    }
}
