<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Exception;

use Symfony\Component\HttpFoundation\Response;

class ResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_force_any_additional_headers()
    {
        /** @var ResponseException $e */
        $e = $this->getMockForAbstractClass(ResponseException::class);

        $this->assertEquals(
            [],
            $e->getHeaders()
        );
    }

    /**
     * @test
     */
    public function it_uses_the_exception_code_as_status_code()
    {
        /** @var ResponseException $e */
        $e = $this->getMockForAbstractClass(
            ResponseException::class,
            [
                'The exception message',
                Response::HTTP_BAD_REQUEST
            ]
        );

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $e->getStatusCode()
        );
    }
}
