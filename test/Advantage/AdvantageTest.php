<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class AdvantageTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Advantage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $advantage;

    /**
     * @var AdvantageType
     */
    protected $type;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $points;

    /**
     * @var bool
     */
    protected $exchangeable;

    public function setUp()
    {
        $this->type = AdvantageType::WELCOME();
        $this->id = new StringLiteral('10');
        $this->title = new StringLiteral('Some title');
        $this->points = new Integer(5);
        $this->exchangeable = true;

        // Instantiate an advantage object, with any abstract methods implemented as mocks.
        // Concrete methods will remain implemented as-is.
        $this->advantage = $this->getMockForAbstractClass(
            Advantage::class,
            [
                $this->type,
                $this->id,
                $this->title,
                $this->points,
                $this->exchangeable,
            ]
        );
    }

    /**
     * @test
     */
    public function it_creates_an_advantage_identifier_and_makes_it_available()
    {
        $expected = AdvantageIdentifier::fromAdvantageTypeAndId(
            $this->type,
            $this->id
        );
        $actual = $this->advantage->getIdentifier();

        $this->assertTrue($expected->sameValueAs($actual));
    }

    /**
     * @test
     */
    public function it_can_return_the_data_from_the_constructor()
    {
        $this->assertEquals($this->type, $this->advantage->getType());
        $this->assertEquals($this->id, $this->advantage->getId());
        $this->assertEquals($this->title, $this->advantage->getTitle());
        $this->assertEquals($this->points, $this->advantage->getPoints());
        $this->assertEquals($this->exchangeable, $this->advantage->isExchangeable());
    }

    /**
     * @test
     * @dataProvider exchangeStatusProvider
     *
     * @param $exchangeable
     * @param $expected
     */
    public function it_makes_sure_that_the_is_exchangeable_method_returns_boolean(
        $exchangeable,
        $expected
    ) {
        /* @var Advantage $advantage */
        $advantage = $this->getMockForAbstractClass(
            Advantage::class,
            [
                $this->type,
                $this->id,
                $this->title,
                $this->points,
                $exchangeable
            ]
        );

        $this->assertTrue($advantage->isExchangeable() === $expected);
    }

    /**
     * @return array
     */
    public function exchangeStatusProvider()
    {
        return [
            [1, true],
            ["1", true],
            ["yes", true],
            [0, false],
            ["0", false],
            ["no", true],
            [null, false],
        ];
    }

    /**
     * @test
     */
    public function it_is_serializable()
    {
        $json = json_encode($this->advantage);
        $this->assertJsonEquals($json, 'Advantage/data/advantage.json');
    }
}
