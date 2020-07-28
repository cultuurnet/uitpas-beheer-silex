<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class OptInPreferencesJsonDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->serviceMails)) {
            throw new MissingPropertyException('serviceMails');
        }

        if (!isset($data->milestoneMails)) {
            throw new MissingPropertyException('milestoneMails');
        }

        if (!isset($data->infoMails)) {
            throw new MissingPropertyException('infoMails');
        }

        if (!isset($data->sms)) {
            throw new MissingPropertyException('sms');
        }

        if (!isset($data->post)) {
            throw new MissingPropertyException('post');
        }

        // Even though there are 3 possible preference (all, notifications, none), the front-end application only has
        // a checkbox and POSTs a boolean value. It has been decided that true = all, and false = notifications.
        return new OptInPreferences(
            (bool) $data->serviceMails,
            (bool) $data->milestoneMails,
            (bool) $data->infoMails,
            (bool) $data->sms,
            (bool) $data->post
        );
    }
}
