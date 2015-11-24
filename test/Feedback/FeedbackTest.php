<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

class FeedbackTest extends \PHPUnit_Framework_TestCase
{
    use FeedbackTestDataTrait;

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $feedback = $this->getFeedback();
        $this->assertEquals($this->getName(), $feedback->getName());
        $this->assertEquals($this->getEmail(), $feedback->getEmail());
        $this->assertEquals($this->getCounter(), $feedback->getCounterName());
        $this->assertEquals($this->getMessage(), $feedback->getMessage());
    }
}
