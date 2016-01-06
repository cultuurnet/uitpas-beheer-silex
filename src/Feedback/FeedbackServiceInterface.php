<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

interface FeedbackServiceInterface
{
    public function send(Feedback $feedback);
}
