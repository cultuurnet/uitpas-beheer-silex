<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolJsonDeserializer extends JSONDeserializer
{
    /**
     * @inheritdoc
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize(
            $data
        );

        if (empty($data->id)) {
            throw new MissingPropertyException('id');
        }

        $name = null;
        if (!empty($data->name)) {
            $name = new StringLiteral($data->name);
        }

        $school = new School(
            new StringLiteral((string) $data->id),
            $name
        );

        return $school;
    }
}
