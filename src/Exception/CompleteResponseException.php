<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class CompleteResponseException extends ResponseException implements
    ReadableCodeExceptionInterface,
    ContextualExceptionInterface
{
    /**
     * @var string
     */
    protected $readableCode;

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @param string $message
     * @param string $readableCode
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $readableCode, $code = 400, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setReadableCode($readableCode);
    }

    /**
     * @param string $readableCode
     */
    public function setReadableCode($readableCode)
    {
        $this->readableCode = $readableCode;
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return $this->readableCode;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param \CultureFeed_Exception $exception
     * @param int $code
     *
     * @return static
     */
    public static function fromCultureFeedException(\CultureFeed_Exception $exception, $code = 400)
    {
        // The property 'error_code' is defined in the CultureFeed-PHP library, so there's no way for us to fix this
        // coding standards issue.
        // @codingStandardsIgnoreStart
        $readableCode = $exception->error_code;
        // @codingStandardsIgnoreEnd

        if ($readableCode === 'ACCESS_DENIED') {
            $code = '403';
        }

        return new static(
            $exception->getMessage(),
            $readableCode,
            $code,
            $exception
        );
    }
}
