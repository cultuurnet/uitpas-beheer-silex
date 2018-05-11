<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

final class OptInPreferences implements \JsonSerializable
{
    /**
     * True if the passholder has opted in to receive service mails (only used for registration).
     *
     * @var bool
     */
    protected $optInServiceMails;

    /**
     * True if the passholder has opted in to receive milestone mails (only used for registration).
     *
     * @var bool
     */
    protected $optInMilestoneMails;

    /**
     * True if the passholder has opted in to receive info mails (only used for registration).
     *
     * @var bool
     */
    protected $optInInfoMails;

    /**
     * True if the passholder has opted in to receive SMS messages (only used for registration).
     *
     * @var bool
     */
    protected $optInSms;

    /**
     * True if the passholder has opted in to receive info via post (only used for registration).
     *
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
     *
     * @param \CultureFeed_Uitpas_Passholder_OptInPreferences $optInPreferences
     * @return self
     */
    public static function fromCultureFeedOptInPreferences(\CultureFeed_Uitpas_Passholder_OptInPreferences $optInPreferences)
    {
        $optInServiceMails = $optInPreferences->optInServiceMails;
        $optInMilestoneMails = $optInPreferences->optInMilestoneMails;
        $optInInfoMails = $optInPreferences->optInInfoMails;
        $optInSms = $optInPreferences->optInSms;
        $optInPost = $optInPreferences->optInPost;

        return new self($optInServiceMails, $optInMilestoneMails, $optInInfoMails, $optInSms, $optInPost);
    }
}
