<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Session\UserSession;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CounterControllerProvider implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->before(
            function (Request $request, Application $app) {
                if (is_null($app['uitid_current_user'])) {
                    return new Response('Access denied', 403);
                } else {
                    return null;
                }
            }
        );

        $controllers->get(
            '/',
            function (Request $request, Application $app) {
                /* @var \CultuurNet\UiTPASBeheer\Counter\CounterService $counterService */
                $counterService = $app['culturefeed_counters'];
                return new JsonResponse($counterService->getCounters($app['uitid_current_user']));
            }
        );

        $controllers->post(
            '/current',
            function (Request $request, Application $app) {
                $counterId = $request->request->get('counterId');
                $counters = $this->getCounters($app);
                if (in_array($counterId, array_keys($counters))) {
                    /* @var UserSession $session */
                    $session = $app['session'];
                    $session->setCounterId($counterId);
                    return new Response('', 200);
                } else {
                    return new Response('Access denied: Counter ID not valid for the current user.', 403);
                }
            }
        );

        $controllers->get(
            '/current',
            function (Request $request, Application $app) {
                /* @var UserSession $session */
                $session = $app['session'];
                return new JsonResponse(array(
                    'counterId' => $session->getCounterId(),
                ));
            }
        );

        return $controllers;
    }
}
