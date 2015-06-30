<?php

namespace CultuurNet\UiTPASBeheer\Counter;

class CounterConsumerKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_when_an_empty_value_is_provided()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new CounterConsumerKey('');
    }
}
