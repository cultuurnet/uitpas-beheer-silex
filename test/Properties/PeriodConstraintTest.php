<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Number\Integer;

class PeriodConstraintTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var PeriodType
     */
    private $type;

    /**
     * @var \ValueObjects\Number\Integer
     */
    private $volume;

    /**
     * @var PeriodConstraint
     */
    private $periodConstraint;

    public function setUp()
    {
        $this->type = PeriodType::YEAR();
        $this->volume = new Integer(5);

        $this->periodConstraint = new PeriodConstraint($this->type, $this->volume);
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $this->assertEquals($this->type, $this->periodConstraint->getType());
        $this->assertEquals($this->volume, $this->periodConstraint->getVolume());
    }

    /**
     * @test
     */
    public function it_can_be_encoded_to_json()
    {
        $json = json_encode($this->periodConstraint);
        $this->assertJsonEquals($json, 'Properties/data/period-constraint.json');
    }

    /**
     * @test
     */
    public function it_can_be_created_from_a_culturefeed_period_constraint()
    {
        $cfPeriodConstraint = new \CultureFeed_Uitpas_PeriodConstraint();
        $cfPeriodConstraint->type = 'YEAR';
        $cfPeriodConstraint->volume = 5;

        $periodConstraint = PeriodConstraint::fromCulturefeedPeriodConstraint($cfPeriodConstraint);

        $this->assertTrue($this->periodConstraint->sameValueAs($periodConstraint));
    }
}
