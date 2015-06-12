<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;

class CounterNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * CounterNotFoundException
     */
    private $e;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->e = new CounterNotFoundException(
            '10'
        );
    }

    /**
     * @test
     */
    public function it_mentions_which_counter_could_not_be_found()
    {
        $this->assertEquals(
            'The counter with id 10 was not found.',
            $this->e->getMessage()
        );
    }

    /**
     * @test
     */
    public function it_has_a_readable_code()
    {
        $this->assertInstanceOf(
            ReadableCodeExceptionInterface::class,
            $this->e
        );

        $this->assertEquals(
            'COUNTER_NOT_FOUND',
            $this->e->getReadableCode()
        );
    }
}
