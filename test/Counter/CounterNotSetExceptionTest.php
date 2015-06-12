<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Counter;

use CultureFeed_User;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class CounterNotSetExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterNotSetException
     */
    private $e;

    /**
     * @var CultureFeed_User
     */
    private $user;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->user = new CultureFeed_User();
        $this->user->nick = 'JohnDoe';

        $this->e = new CounterNotSetException(
            $this->user
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
            'COUNTER_NOT_SET',
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
        $e = new CounterNotSetException(
            $this->user,
            Response::HTTP_BAD_REQUEST
        );

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $e->getStatusCode()
        );
    }
}
