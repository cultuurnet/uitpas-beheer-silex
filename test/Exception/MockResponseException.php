<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class MockResponseException extends ResponseException
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setHeaders();
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers = array())
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
