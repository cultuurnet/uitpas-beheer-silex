<?php

namespace CultuurNet\UiTPASBeheer\Response;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\Exception\MockReadableCodeException;
use CultuurNet\UiTPASBeheer\Exception\MockResponseException;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class JsonErrorResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var array
     */
    protected $headers;

    public function setUp()
    {
        $this->message = "Error message.";
        $this->code = 400;

        $this->headers = array(
            "Foo" => "Bar",
        );
    }

    /**
     * @test
     */
    public function it_provides_error_information()
    {
        $exception = new MockResponseException($this->message, $this->code);
        $exception->setHeaders($this->headers);

        $data = $this->getResponseDataForException($exception);

        $expected = new \stdClass();
        $expected->type = "error";
        $expected->exception = MockResponseException::class;
        $expected->message = $this->message;
        $expected->code = $this->code;

        $this->assertEquals($expected, $data);
    }

    /**
     * @test
     */
    public function it_uses_a_readable_code_if_provided()
    {
        $readableCode = 'MOCK_CODE';

        $exception = new MockReadableCodeException($this->message, $this->code);
        $exception->setReadableCode($readableCode);

        $data = $this->getResponseDataForException($exception);

        $expected = new \stdClass();
        $expected->type = "error";
        $expected->exception = MockReadableCodeException::class;
        $expected->message = $this->message;
        $expected->code = $readableCode;

        $this->assertEquals($expected, $data);
    }

    /**
     * @test
     */
    public function it_adds_context_information_if_provided()
    {
        $context = 'This can be anything you want.';

        $exception = new CompleteResponseException($this->message, $this->code);
        $exception->setContext($context);

        $data = $this->getResponseDataForException($exception);

        $expected = new \stdClass();
        $expected->type = "error";
        $expected->exception = CompleteResponseException::class;
        $expected->message = $this->message;
        $expected->code = $this->code;
        $expected->context = $context;

        $this->assertEquals($expected, $data);
    }

    /**
     * @param ResponseException $exception
     * @return array
     */
    private function getResponseDataForException(ResponseException $exception)
    {
        $response = new JsonErrorResponse($exception);

        $content = $response->getContent();
        $data = json_decode($content);

        return $data;
    }

    /**
     * @test
     */
    public function it_sets_the_http_status_code()
    {
        $exception = new MockResponseException($this->message, $this->code);
        $exception->setHeaders($this->headers);

        $response = new JsonErrorResponse($exception);

        $this->assertEquals($this->code, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_sets_the_http_headers()
    {
        $exception = new MockResponseException($this->message, $this->code);
        $exception->setHeaders($this->headers);

        $response = new JsonErrorResponse($exception);

        foreach ($this->headers as $header => $value) {
            $this->assertEquals($value, $response->headers->get($header));
        }
    }
}
