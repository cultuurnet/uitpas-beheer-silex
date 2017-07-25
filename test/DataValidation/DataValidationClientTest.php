<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use CultuurNet\UiTPASBeheer\DataValidation\Item\RealtimeValidationResult;

class DataValidationClientTest extends AbstractDataValidationClientTest
{
    /**
     * @test
     */
    public function it_responds_the_result_for_realtime_validation()
    {
        $client = $this->getMockClient('realtimeValidation.json');
        $result = $client->realtimeValidateEmail('test@test.com');

        $this->assertInstanceOf(RealtimeValidationResult::class, $result, 'It returns a realtime validation result');
        $this->assertEquals('F', $result->getGrade(), 'It has the correct grade');
        $this->assertEquals(RealtimeValidationResult::REALTIME_VALIDATION_RESULT_STATUS_OK, $result->getStatus(), 'It has the correct status');
        $this->assertEquals(null, $result->getReason(), 'It contains no error message');
    }
}
