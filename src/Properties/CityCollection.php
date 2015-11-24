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
}
