<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class FeedbackControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['feedback_controller'] = $app->share(
            function (Application $app) {
                $controller = new FeedbackController(
                    $app['feedback_service'],
                    $app['feedback_json_deserializer']
                );

                return $controller;
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->post('/feedback', 'feedback_controller:send');

        return $controllers;
    }
}
