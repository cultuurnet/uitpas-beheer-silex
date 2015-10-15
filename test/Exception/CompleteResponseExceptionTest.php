<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class CompleteResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $readableCode;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var \CultureFeed_Exception
     */
    protected $cultureFeedException;

    public function setUp()
    {
        $this->message = 'Something went wrong.';
        $this->readableCode = 'SOMETHING_WRONG';
        $this->code = 500;

        $this->cultureFeedException = new \CultureFeed_Exception(
            $this->message,
            $this->readableCode
        );
    }

    /**
     * @test
     */
    public function it_has_a_configurable_readable_code()
    {
        $exception = new CompleteResponseException(
            $this->message,
            $this->readableCode,
            $this->code
        );

        $this->assertEquals($this->readableCode, $exception->getReadableCode());

        $exception->setReadableCode('OTHER_CODE');
        $this->assertEquals('OTHER_CODE', $exception->getReadableCode());
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_exception()
    {
        $exception = CompleteResponseException::fromCultureFeedException(
            $this->cultureFeedException,
            $this->code
        );

        $this->assertEquals($this->message, $exception->getMessage());
        $this->assertEquals($this->readableCode, $exception->getReadableCode());
        $this->assertEquals($this->code, $exception->getCode());

        $accessDeniedException = new \CultureFeed_Exception('Access denied.', 'ACCESS_DENIED');
        $exception = CompleteResponseException::fromCultureFeedException(
            $accessDeniedException
        );

        $this->assertEquals(403, $exception->getCode());
    }
}
