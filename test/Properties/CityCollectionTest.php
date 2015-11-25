<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 24/11/15
 * Time: 10:06
 */

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

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

    /**
     * @test
     */
    public function it_can_return_a_collection_from_culturefeed_PointsPromotion()
    {
        $id = new StringLiteral('10');
        $title = new StringLiteral('Delicious coffee');
        $points = new Integer(5);
        $exchangeable = true;

        $validForCities = [
            "Brussel",
            "Leuven",
            "Ninove",
        ];

        $cfPromotion = new \CultureFeed_Uitpas_Passholder_PointsPromotion(
            $id,
            $title,
            $points
        );

        if ($exchangeable) {
            $cfPromotion->cashInState = \CultureFeed_Uitpas_Passholder_PointsPromotion::CASHIN_POSSIBLE;
        } else {
            $cfPromotion->cashInState =
                \CultureFeed_Uitpas_Passholder_PointsPromotion::CASHIN_NOT_POSSIBLE_POINTS_CONSTRAINT;
        }

        $cfPromotion->validForCities = $validForCities;

        $cityCollection = CityCollection::fromCultureFeedAdvantage($cfPromotion);

        $expectedCityCollection = $this->collection;

        $this->assertEquals(
            $expectedCityCollection,
            $cityCollection
        );
    }

    /**
     * @test
     */
    public function it_can_return_a_collection_from_culturefeed_welcome_advantage()
    {
        $cfAdvantage = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantage();
        $cfAdvantage->id = new StringLiteral('10');
        $cfAdvantage->title = new StringLiteral('Delicious coffee');
        $cfAdvantage->cashedIn = false;

        $cfAdvantage->validForCities = [
            "Brussel",
            "Leuven",
            "Ninove",
        ];

        $cityCollection = CityCollection::fromCultureFeedAdvantage($cfAdvantage);

        $expectedCityCollection = $this->collection;

        $this->assertEquals(
            $expectedCityCollection,
            $cityCollection
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_using_wrong_culturefeed_argument()
    {
        $wrongCfAdvantage = new \CultureFeed_Cdb_Data_Address();

        $this->setExpectedException(\InvalidArgumentException::class);

        CityCollection::fromCultureFeedAdvantage($wrongCfAdvantage);
    }
}
