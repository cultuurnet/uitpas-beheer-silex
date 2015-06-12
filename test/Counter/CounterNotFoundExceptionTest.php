<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @test
     */
    public function it_defaults_to_status_code_http_not_found()
    {
        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $this->e->getStatusCode()
        );
    }

    /**
     * @test
     */
    public function it_allows_to_change_the_status_code()
    {
        $e = new CounterNotFoundException('10', Response::HTTP_BAD_REQUEST);

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $e->getStatusCode()
        );
    }
}
