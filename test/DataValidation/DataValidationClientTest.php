<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use CultuurNet\UiTPASBeheer\DataValidation\Item\EmailValidationResult;

class DataValidationClientTest extends AbstractDataValidationClientTest
{
    /**
     * @test
     */
    public function it_responds_the_result_for_email_validation()
    {
        $client = $this->getMockClient('emailValidation.json');
        $result = $client->validateEmail('test@domain.com');

        $this->assertInstanceOf(EmailValidationResult::class, $result, 'It returns a realtime validation result');
        $this->assertEquals('F', $result->getGrade(), 'It has the correct grade');
        $this->assertEquals(EmailValidationResult::REALTIME_VALIDATION_RESULT_STATUS_OK, $result->getStatus(), 'It has the correct status');
        $this->assertEquals(null, $result->getReason(), 'It contains no error message');
    }
}
