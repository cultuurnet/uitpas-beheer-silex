<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Properties\City;
use CultuurNet\UiTPASBeheer\Properties\CityCollection;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class WelcomeAdvantage extends Advantage
{
    /**
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param bool $exchangeable
     */
    public function __construct(StringLiteral $id, StringLiteral $title, $exchangeable)
    {
        parent::__construct(
            AdvantageType::WELCOME(),
            $id,
            $title,
            new Integer(0),
            $exchangeable
        );
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_WelcomeAdvantage $welcomeAdvantage
     * @return static
     */
    public static function fromCultureFeedWelcomeAdvantage(
        \CultureFeed_Uitpas_Passholder_WelcomeAdvantage $welcomeAdvantage
    ) {
        $id = new StringLiteral((string) $welcomeAdvantage->id);
        $title = new StringLiteral((string) $welcomeAdvantage->title);

        $exchangeable = !$welcomeAdvantage->cashedIn &&
            (is_null($welcomeAdvantage->maxAvailableUnits) ||
                $welcomeAdvantage->maxAvailableUnits > $welcomeAdvantage->unitsTaken);

        $advantage = new static(
            $id,
            $title,
            $exchangeable
        );

        if (!empty($welcomeAdvantage->description1)) {
            $advantage = $advantage->withDescription1(new StringLiteral($welcomeAdvantage->description1));
        }

        if (!empty($welcomeAdvantage->description2)) {
            $advantage = $advantage->withDescription2(new StringLiteral($welcomeAdvantage->description2));
        }

        if (!empty($welcomeAdvantage->validForCities)) {
            $cityCollection = new CityCollection();

            foreach ($welcomeAdvantage->validForCities as $city) {
                $cityCollection = $cityCollection->with(new City($city));
            }

            $advantage = $advantage->withValidForCities($cityCollection);
        }

        if (!empty($welcomeAdvantage->counters)) {
            $advantage = $advantage->withValidForCounters($welcomeAdvantage->counters);
        }

        if (!empty($welcomeAdvantage->cashingPeriodEnd)) {
            $dateParts = explode('-', $welcomeAdvantage->cashingPeriodEnd);
            $dateParts[1] = $dateParts[1] - 1;
            $advantage = $advantage->withEndDate(
                new Date(
                    new Year($dateParts[0]),
                    Month::getByOrdinal($dateParts[1]),
                    new MonthDay((int) $dateParts[2])
                )
            );
        }

        return $advantage;
    }
}
