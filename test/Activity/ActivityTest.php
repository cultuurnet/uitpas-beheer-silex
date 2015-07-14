<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var StringLiteral
     */
    protected $date;

    /**
     * @var StringLiteral
     */
    protected $description;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $title;

    public function setUp()
    {
        $this->id = new StringLiteral('10');
        $this->title = new StringLiteral('Some title');
        $this->description = new StringLiteral('Some description');
        $this->date = new StringLiteral('yesterday');
        $this->activity = new Activity(
            $this->id,
            $this->title,
            $this->description,
            $this->date
        );
    }

    /**
     * @test
     */
    public function it_can_return_the_data_from_the_constructor()
    {
        $this->assertEquals($this->id, $this->activity->getId());
        $this->assertEquals($this->title, $this->activity->getTitle());
        $this->assertEquals($this->description, $this->activity->getDescription());
        $this->assertEquals($this->date, $this->activity->getDate());
    }
}
