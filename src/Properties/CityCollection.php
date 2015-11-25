<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 24/11/15
 * Time: 10:02
 */

namespace CultuurNet\UiTPASBeheer\Properties;

use TwoDotsTwice\Collection\AbstractCollection;

class CityCollection extends AbstractCollection implements \JsonSerializable
{
    /**
     * @return string
     */
    protected function getValidObjectType()
    {
        return City::class;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param $advantage
     * @return CityCollection|AbstractCollection|static
     */
    public static function fromCultureFeedAdvantage($advantage)
    {
        if (!$advantage instanceof \CultureFeed_Uitpas_Passholder_WelcomeAdvantage
        && !$advantage instanceof \CultureFeed_Uitpas_Passholder_PointsPromotion) {
            throw new \InvalidArgumentException();
        }

        $cityCollection = new CityCollection();

        foreach ($advantage->validForCities as $city) {
            $cityCollection = $cityCollection->with(new City($city));
        }

        return $cityCollection;
    }
}
