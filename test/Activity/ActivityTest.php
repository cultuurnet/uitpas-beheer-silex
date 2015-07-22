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
    protected $when;

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
        $this->when = new StringLiteral('yesterday');
        $this->activity = new Activity(
            $this->id,
            $this->title
        );
        $this->activity = $this->activity
            ->withWhen($this->when)
            ->withDescription($this->description);
    }

    /**
     * @test
     */
    public function it_can_return_the_data_from_the_constructor()
    {
        $this->assertEquals($this->id, $this->activity->getId());
        $this->assertEquals($this->title, $this->activity->getTitle());
        $this->assertEquals($this->description, $this->activity->getDescription());
        $this->assertEquals($this->when, $this->activity->getWhen());
    }

    /**
     * @test
     */
    public function it_can_be_json_encoded()
    {
        $this->assertJsonEquals(
            json_encode($this->activity),
            'Activity/data/activity.json'
        );
    }
}
