<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class WelcomeAdvantageService extends CounterAwareUitpasService implements AdvantageServiceInterface
{
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
     * @return WelcomeAdvantage[]
     */
    public function getExchangeable(UiTPASNumber $uitpasNumber)
    {
        $advantages = array();

        $options = new \CultureFeed_Uitpas_Passholder_Query_WelcomeAdvantagesOptions();

        $options->balieConsumerKey = $this->getCounterConsumerKey();
        $options->cashInBalieConsumerKey = $this->getCounterConsumerKey();
        $options->uitpas_number = $uitpasNumber->toNative();

        $options->cashingPeriodBegin = time();
        $options->cashingPeriodEnd = time();

        $options->cashedIn = false;

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
