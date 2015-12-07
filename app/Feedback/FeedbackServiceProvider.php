<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use CultuurNet\UiTPASBeheer\Feedback\FeedbackEmailService;
use Silex\Application;
use Silex\ServiceProviderInterface;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class FeedbackServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
        $app['feedback_json_deserializer'] = $app->share(
            function () {
                return new FeedbackJsonDeserializer();
            }
        );

        $app['feedback_service'] = $app->share(
            function (Application $app) {
                return new FeedbackEmailService(
                    $app['mailer'],
                    new EmailAddress($app['feedback.to']),
                    new StringLiteral($app['feedback.subject'])
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {
    }
}
