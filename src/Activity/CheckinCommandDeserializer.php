<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;
use CultuurNet\UiTPASBeheer\Activity\CheckinCommand;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;

class CheckinCommandDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->eventCdbid)) {
            throw new MissingPropertyException('event cdbid');
        }

        $checkinCommand = new CheckinCommand(
            new Cdbid((string) $data->eventCdbid)
        );

        return $checkinCommand;
    }
}
