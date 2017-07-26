<?php

namespace CultuurNet\ProjectAanvraag\Insightly\Item;

use CultuurNet\UiTPASBeheer\DataValidation\AbstractDataValidationClientTest;
use CultuurNet\UiTPASBeheer\DataValidation\Item\EmailValidationResult;

class EmailValidationResultTest extends AbstractDataValidationClientTest
{
    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $grade;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var EmailValidationResult
     */
    protected $realtimeValidationResult;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->status = 'ok';
        $this->grade = 'F';
        $this->reason = null;

        $realtimeValidationResult = new EmailValidationResult();
        $realtimeValidationResult->setStatus($this->status);
        $realtimeValidationResult->setGrade($this->grade);
        $realtimeValidationResult->setReason($this->reason);

        $this->realtimeValidationResult = $realtimeValidationResult;
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $this->assertEquals(
            $this->status,
            $this->realtimeValidationResult->getStatus()
        );

        $this->assertEquals(
            $this->grade,
            $this->realtimeValidationResult->getGrade()
        );

        $this->assertEquals(
            $this->reason,
            $this->realtimeValidationResult->getReason()
        );

        $this->assertEquals(
            true,
            $this->realtimeValidationResult->isOK()
        );
    }
}
