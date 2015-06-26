<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;

class PassHolderUpdateExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_includes_the_previous_exception_message()
    {
        $previous = new \CultureFeed_Exception('Lorem Ipsum.', 500);
        $updateException = new PassHolderUpdateException(500, $previous);

        $this->assertContains($previous->getMessage(), $updateException->getMessage());
    }

    /**
     * @test
     */
    public function it_has_a_readable_error_code()
    {
        $updateException = new PassHolderUpdateException();
        $this->assertInstanceOf(ReadableCodeExceptionInterface::class, $updateException);
        $this->assertEquals('PASSHOLDER_UPDATE_CULTUREFEED_ERROR', $updateException->getReadableCode());
    }
}
