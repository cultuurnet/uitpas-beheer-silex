<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class DateRangeJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     *
     * @return DateRange
     *
     * @throws IncorrectParameterValueException
     *   When 'from' or 'to' are in an incorrect format.
     *
     * @throws MissingPropertyException
     *   When 'from' or 'to' are missing.
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->from)) {
            throw new MissingPropertyException('from');
        }
        if (!isset($data->to)) {
            throw new MissingPropertyException('to');
        }

        $from = \DateTime::createFromFormat('Y-m-d', $data->from);
        if (!$from) {
            throw new IncorrectParameterValueException('from');
        }

        $to = \DateTime::createFromFormat('Y-m-d', $data->to);
        if (!$to) {
            throw new IncorrectParameterValueException('to');
        }

        $from = Date::fromNativeDateTime($from);
        $to = Date::fromNativeDateTime($to);

        try {
            return new DateRange($from, $to);
        } catch (\InvalidArgumentException $e) {
            throw new IncorrectParameterValueException('to');
        }
    }
}
