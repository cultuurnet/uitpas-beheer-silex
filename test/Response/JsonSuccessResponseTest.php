<?php

namespace CultuurNet\UiTPASBeheer\Response;

class JsonSuccessResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $message;

    public function setUp()
    {
        $this->message = "Error message.";
    }

    /**
     * @test
     */
    public function it_provides_success_information()
    {
        $response = new JsonSuccessResponse($this->message);

        $content = $response->getContent();
        $data = json_decode($content);

        $expected = new \stdClass();
        $expected->type = "success";
        $expected->message = $this->message;

        $this->assertEquals($expected, $data);
    }
}
