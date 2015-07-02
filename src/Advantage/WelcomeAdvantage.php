<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

class WelcomeAdvantage extends Advantage
{
    /**
     * @param string $id
     * @param string $title
     */
    public function __construct($id, $title = '')
    {
        parent::__construct(AdvantageType::WELCOME(), $id, $title, 0);
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_WelcomeAdvantage $advantage
     * @return static
     */
    public static function fromCultureFeedWelcomeAdvantage(\CultureFeed_Uitpas_Passholder_WelcomeAdvantage $advantage)
    {
        return new static(
            $advantage->id,
            $advantage->title
        );
    }
}
