<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class PrivacyPreferencesJsonDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->email)) {
            throw new MissingPropertyException('email');
        }
        if (!isset($data->sms)) {
            throw new MissingPropertyException('sms');
        }

        // Even though there are 3 possible preference (all, notifications, none), the front-end application only has
        // a checkbox and POSTs a boolean value. It has been decided that true = all, and false = notifications.
        return new PrivacyPreferences(
            ((bool) $data->email) ? PrivacyPreferenceEmail::ALL() : PrivacyPreferenceEmail::NOTIFICATION(),
            ((bool) $data->sms) ? PrivacyPreferenceSMS::ALL() : PrivacyPreferenceSMS::NOTIFICATION()
        );
    }
}
