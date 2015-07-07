<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

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
     * @param \CultureFeed_Uitpas_Passholder_WelcomeAdvantage $advantage
     * @return static
     */
    public static function fromCultureFeedWelcomeAdvantage(\CultureFeed_Uitpas_Passholder_WelcomeAdvantage $advantage)
    {
        $id = new StringLiteral((string) $advantage->id);
        $title = new StringLiteral((string) $advantage->title);

        $exchangeable = !$advantage->cashedIn &&
            (is_null($advantage->maxAvailableUnits) || $advantage->maxAvailableUnits > $advantage->unitsTaken);

        return new static(
            $id,
            $title,
            $exchangeable
        );
    }
}
