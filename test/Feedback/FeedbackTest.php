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
        $this->assertEquals($this->getFromName(), $feedback->getName());
        $this->assertEquals($this->getFromEmail(), $feedback->getEmail());
        $this->assertEquals($this->getFromCounter(), $feedback->getCounterName());
        $this->assertEquals($this->getMessage(), $feedback->getMessage());
    }
}
