<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;

class PrivacyPreferencesTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var PrivacyPreferenceEmail
     */
    protected $emailPreference;

    /**
     * @var PrivacyPreferenceSMS
     */
    protected $smsPreference;

    /**
     * @var PrivacyPreferences
     */
    protected $privacyPreferences;

    public function setUp()
    {
        $this->emailPreference = PrivacyPreferenceEmail::ALL();
        $this->smsPreference = PrivacyPreferenceSMS::NO();

        $this->privacyPreferences = new PrivacyPreferences(
            $this->emailPreference,
            $this->smsPreference
        );
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->privacyPreferences);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/privacy-preferences-complete.json');
    }

    /**
     * @test
     */
    public function it_encodes_notifications_only_as_false_in_json()
    {
        $privacyPreferences = new PrivacyPreferences(
            $this->emailPreference,
            PrivacyPreferenceSMS::NOTIFICATION()
        );

        $json = json_encode($privacyPreferences);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/privacy-preferences-complete.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->secondName = 'Zooni';
        $cfPassHolder->emailPreference = 'ALL_MAILS';
        $cfPassHolder->smsPreference = 'NO_SMS';

        $privacyPreferences = PrivacyPreferences::fromCultureFeedPassHolder($cfPassHolder);
        $this->assertJsonEquals(json_encode($privacyPreferences), 'PassHolder/data/properties/privacy-preferences-complete.json');
    }
}
