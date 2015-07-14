<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class ReadableCodeResponseException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @var string
     */
    protected $readableCode;

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
     * @param \CultureFeed_Exception $exception
     * @param int $code
     *
     * @return static
     */
    public static function fromCultureFeedException(\CultureFeed_Exception $exception, $code = 400)
    {
        $readableCode = $exception->error_code;

        if ($readableCode === 'ACCESS_DENIED') {
            $code = '403';
        }

        return new static($exception->getMessage(), $readableCode, $code, $exception);
    }
}
