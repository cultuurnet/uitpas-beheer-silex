<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class FeedbackEmailService implements FeedbackServiceInterface
{
    /**
     * @var \Swift_Mailer
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
     * @param \Swift_Mailer $mailer
     * @param EmailAddress $to
     * @param StringLiteral $subject
     */
    public function __construct(
        \Swift_Mailer $mailer,
        EmailAddress $to,
        StringLiteral $subject
    ) {
        $this->mailer = $mailer;
        $this->to = $to;
        $this->subject = $subject;
    }

    /**
     * @param Feedback $feedback
     *
     * @throws FeedbackNotSentException
     *   When the feedback could not be sent, for any reason.
     */
    public function send(Feedback $feedback)
    {
        $message = \Swift_Message::newInstance();

        $message->setTo($this->to->toNative());

        $message->addFrom(
            $feedback->getEmail()->toNative(),
            $feedback->getName()->toNative()
        );

        $message->setSubject($this->subject->toNative());

        $message->setBody(
            sprintf(
                'Afzender: %s (%s)' . PHP_EOL .
                'Balie: %s' . PHP_EOL .
                PHP_EOL .
                '%s',
                $feedback->getName()->toNative(),
                $feedback->getEmail()->toNative(),
                $feedback->getCounterName()->toNative(),
                $feedback->getMessage()->toNative()
            )
        );

        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            // Exception type is not documented in Transporter interface and
            // varies per implementation, so just catch all exceptions.
            throw new FeedbackNotSentException($e);
        }
    }
}
