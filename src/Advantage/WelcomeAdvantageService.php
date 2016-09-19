<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class WelcomeAdvantageService extends CounterAwareUitpasService implements AdvantageServiceInterface
{
    /**
     * @var Clock
     */
    protected $clock;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param Clock $clock
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        Clock $clock
    ) {
        parent::__construct(
            $uitpasService,
            $counterConsumerKey
        );

        $this->clock = $clock;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     * @return WelcomeAdvantage|null
     */
    public function get(UiTPASNumber $uitpasNumber, StringLiteral $id)
    {
        $id = $id->toNative();

        $passHolderParameters = new \CultureFeed_Uitpas_Promotion_PassholderParameter();
        $passHolderParameters->uitpasNumber = $uitpasNumber->toNative();

        try {
            $advantage = $this->getUitpasService()->getWelcomeAdvantage($id, $passHolderParameters);
            return WelcomeAdvantage::fromCultureFeedWelcomeAdvantage($advantage);
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param int $max
     * @param int $start
     * @return WelcomeAdvantage[]
     */
    public function getExchangeable(UiTPASNumber $uitpasNumber, $max = null, $start = null)
    {
        $advantages = array();

        $options = new \CultureFeed_Uitpas_Passholder_Query_WelcomeAdvantagesOptions();

        $options->balieConsumerKey = $this->getCounterConsumerKey();
        $options->cashInBalieConsumerKey = $this->getCounterConsumerKey();

        // The property 'uitpas_number' is defined in the CultureFeed-PHP library, so there's no way for us to fix this
        // coding standards issue.
        // @codingStandardsIgnoreStart
        $options->uitpas_number = $uitpasNumber->toNative();
        // @codingStandardsIgnoreEnd

        $currentTimestamp = $this->clock->getDateTime()->getTimestamp();
        $options->cashingPeriodBegin = $options->cashingPeriodEnd = $currentTimestamp;

        $options->cashedIn = false;

        if (!empty($max)) {
            $options->max = $max;
        }

        if (!empty($start)) {
            $options->start = $start;
        }

        $results = $this->getUitpasService()->getWelcomeAdvantagesForPassholder($options);

        foreach ($results->objects as $advantage) {
            $advantages[] = WelcomeAdvantage::fromCultureFeedWelcomeAdvantage($advantage);
        }

        return $advantages;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     */
    public function exchange(UiTPASNumber $uitpasNumber, StringLiteral $id)
    {
        $this->getUitpasService()->cashInWelcomeAdvantage(
            $uitpasNumber->toNative(),
            $id->toNative(),
            $this->getCounterConsumerKey()
        );
    }
}
