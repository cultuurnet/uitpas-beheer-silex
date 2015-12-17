<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use PHPUnit_Framework_TestCase;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PointsTransactionTest extends PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var PointsTransaction|null
     */
    protected $pointsTransaction;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var PointsTransactionType
     */
    protected $type;

    /**
     * @var Date
     */
    protected $creationDate;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $points;

    /**
     * @test
     */
    public function setUp()
    {
        $this->id = new StringLiteral('22');
        $this->type = PointsTransactionType::CASHED_PROMOTION();
        $this->creationDate = new Date(
            new Year('2015'),
            Month::DECEMBER(),
            new MonthDay(4)
        );
        $this->title = new StringLiteral('Some title');
        $this->points = new Integer(5);

        // Instantiate an advantage object, with any abstract methods implemented as mocks.
        // Concrete methods will remain implemented as-is.
        $this->pointsTransaction = $this->getMockForAbstractClass(
            PointsTransaction::class,
            [
                $this->id,
                $this->type,
                $this->creationDate,
                $this->title,
                $this->points,
            ]
        );
    }

    /**
     * @test
     */
    public function it_can_return_the_data_from_the_constructor()
    {
        $this->assertEquals($this->id, $this->pointsTransaction->getId());
        $this->assertEquals($this->type, $this->pointsTransaction->getType());
        $this->assertEquals($this->creationDate, $this->pointsTransaction->getCreationDate());
        $this->assertEquals($this->title, $this->pointsTransaction->getTitle());
        $this->assertEquals($this->points, $this->pointsTransaction->getPoints());
    }

    /**
     * @test
     */
    public function it_is_serializable()
    {
        $json = json_encode($this->pointsTransaction);
        $this->assertJsonEquals($json, 'PointsTransaction/data/abstractPointsTransaction.json');
    }
}
