<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 24/11/15
 * Time: 10:32
 */

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;

class CityTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var City
     */
    protected $leuven;

    /**
     * @test
     */
    public function setUp()
    {
        $this->leuven = new City('Leuven');
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->leuven);
        $this->assertJsonEquals($json, 'Properties/data/city.json');
    }
}
