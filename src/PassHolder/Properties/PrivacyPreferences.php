<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceEmail;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceSMS;

final class PrivacyPreferences implements \JsonSerializable
{
    /**
     * @var PrivacyPreferenceEmail
     */
    protected $emailPreference;

    /**
     * @var PrivacyPreferenceSMS
     */
    protected $smsPreference;

    /**
     * @param PrivacyPreferenceEmail $emailPreference
     * @param PrivacyPreferenceSMS $smsPreference
     */
    public function __construct(
        PrivacyPreferenceEmail $emailPreference,
        PrivacyPreferenceSMS $smsPreference
    ) {
        $this->emailPreference = $emailPreference;
        $this->smsPreference = $smsPreference;
    }

    /**
     * @return PrivacyPreferenceSMS
     */
    public function getSMSPreference()
    {
        return $this->smsPreference;
    }

    /**
     * @return PrivacyPreferenceEmail
     */
    public function getEmailPreference()
    {
        return $this->emailPreference;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $email = $this->emailPreference->is(PrivacyPreferenceEmail::ALL());
        $sms = $this->smsPreference->is(PrivacyPreferenceSMS::ALL());

        return [
            'email' => $email,
            'sms' => $sms,
        ];
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return self
     */
    public static function fromCultureFeedPassHolder(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        $email = PrivacyPreferenceEmail::NO();
        if (!empty($cfPassHolder->emailPreference)) {
            $email = PrivacyPreferenceEmail::get($cfPassHolder->emailPreference);
        }

        $sms = PrivacyPreferenceSMS::NO();
        if (!empty($cfPassHolder->smsPreference)) {
            $sms = PrivacyPreferenceSMS::get($cfPassHolder->smsPreference);
        }

        return new self($email, $sms);
    }
}
