<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class KansenstatuutJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     * @return Kansenstatuut
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->endDate)) {
            throw new MissingPropertyException('endDate');
        }

        $dateTime = new \DateTime($data->endDate);
        $kansenstatuut = new Kansenstatuut(
            Date::fromNativeDateTime($dateTime)
        );

        if (!empty($data->remarks)) {
            $remarks = Remarks::fromNative($data->remarks);
            $kansenstatuut = $kansenstatuut->withRemarks($remarks);
        }

        return $kansenstatuut;
    }
}
