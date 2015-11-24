<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 24/11/15
 * Time: 10:06
 */

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;

class CityCollectionTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var City
     */
    protected $bxl;

    /**
     * @var City
     */
    protected $leuven;

    /**
     * @var City
     */
    protected $ninove;

    /**
     * @var CityCollection
     */
    protected $collection;

    /**
     * @test
     */
    public function setUp()
    {
        $this->bxl = new City('Brussel');
        $this->leuven = new City('Leuven');
        $this->ninove = new City('Ninove');

        $this->collection = (new CityCollection())
            ->with($this->bxl)
            ->with($this->leuven)
            ->with($this->ninove);
    }

    /**
     * @test
     */
    public function it_can_be_encoded_to_json()
    {
        $json = json_encode($this->collection);
        $this->assertJsonEquals($json, 'Properties/data/city-collection.json');
    }
}
