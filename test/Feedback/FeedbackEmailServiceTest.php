<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class FeedbackEmailServiceTest extends \PHPUnit_Framework_TestCase
{
    use FeedbackTestDataTrait;

    /**
     * @var \Swift_Mailer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mailer;

    /**
     * @var EmailAddress
     */
    private $to;

    /**
     * @var StringLiteral
     */
    private $subject;

    /**
     * @var FeedbackEmailService
     */
    private $service;

    public function setUp()
    {
        $callOriginalConstructor = false;
        $this->mailer = $this->getMock(
            \Swift_Mailer::class,
            [],
            [],
            '',
            $callOriginalConstructor
        );

        $this->to = new EmailAddress('foo@bar.com');
        $this->subject = new StringLiteral('Ma Alain toch.');

        $this->service = new FeedbackEmailService(
            $this->mailer,
            $this->to,
            $this->subject
        );
    }

    /**
     * @test
     */
    public function it_sends_feedback_via_mail()
    {
        $expectedMail = new \Swift_Message();

        $expectedMail->setFrom(
            $this->getEmail()->toNative(),
            $this->getName()->toNative()
        );

        $expectedMail->setTo($this->to->toNative());

        $expectedMail->setSubject($this->subject->toNative());

        $expectedMailBody = file_get_contents(__DIR__ . '/data/feedback.txt');
        $expectedMail->setBody($expectedMailBody);

        // Use willReturnCallback so we have access to the actual
        // \Swift_Message to compare with our expected one, as their ids and
        // dates will never be the same.
        $this->mailer->expects($this->once())
            ->method('send')
            ->willReturnCallback(
                function (\Swift_Message $actualMail) use ($expectedMail) {
                    // We don't care what the id and date are, they are not set
                    // manually by us so not in scope of this test.
                    $actualMail->setId($expectedMail->getId());
                    $actualMail->setDate($expectedMail->getDate());

                    $this->assertEquals(
                        $expectedMail->toString(),
                        $actualMail->toString()
                    );
                }
            );

        $this->service->send($this->getFeedback());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_feedback_could_not_be_sent()
    {
        $this->mailer->expects($this->once())
            ->method('send')
            ->willThrowException(
                new \Swift_TransportException('Mail server unreachable.')
            );

        $this->setExpectedException(FeedbackNotSentException::class);

        $this->service->send($this->getFeedback());
    }
}
