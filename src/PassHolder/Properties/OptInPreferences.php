<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;


final class OptInPreferences implements \JsonSerializable
{
    /**
     * @var bool
     */
    protected $optInServiceMails;

    /**
     * @var bool
     */
    protected $optInMilestoneMails;

    /**
     * @var bool
     */
    protected $optInInfoMails;

    /**
     * @var bool
     */
    protected $optInSms;

    /**
     * @var bool
     */
    protected $optInPost;

    /**
     * OptInPreferences constructor.
     * @param bool $optInServiceMails
     * @param bool $optInMilestoneMails
     * @param bool $optInInfoMails
     * @param bool $optInSms
     * @param bool $optInPost
     */
    public function __construct($optInServiceMails, $optInMilestoneMails, $optInInfoMails, $optInSms, $optInPost)
    {
        $this->optInServiceMails = $optInServiceMails;
        $this->optInMilestoneMails = $optInMilestoneMails;
        $this->optInInfoMails = $optInInfoMails;
        $this->optInSms = $optInSms;
        $this->optInPost = $optInPost;
    }

    /**
     * @return bool
     */
    public function hasOptInServiceMails()
    {
        return $this->optInServiceMails;
    }

    /**
     * @return bool
     */
    public function hasOptInMilestoneMails()
    {
        return $this->optInMilestoneMails;
    }

    /**
     * @return bool
     */
    public function hasOptInInfoMails()
    {
        return $this->optInInfoMails;
    }

    /**
     * @return bool
     */
    public function hasOptInSms()
    {
        return $this->optInSms;
    }

    /**
     * @return bool
     */
    public function hasOptInPost()
    {
        return $this->optInPost;
    }

    /**
     * @param OptInPreferences $other
     * @return bool
     */
    public function sameValueAs(OptInPreferences $other)
    {
        return $this->jsonSerialize() === $other->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'optInServiceMails' => $this->hasOptInServiceMails(),
            'optInMilestoneMails' => $this->hasOptInMilestoneMails(),
            'optInInfoMails' => $this->hasOptInInfoMails(),
            'optInPost' => $this->hasOptInPost(),
            'optInSms' => $this->hasOptInSms(),
        ];
    }

    /**
     * TODO
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return self
     *
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
     * */
}
