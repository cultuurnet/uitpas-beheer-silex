<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

        $controllers->get(
            '/',
            function (Request $request, Application $app) {
                /* @var \CultuurNet\UiTPASBeheer\Counter\CounterService $counterService */
                $counterService = $app['uitpas_counter_service'];
                return new JsonResponse($counterService->getCounters($app['uitid_current_user']));
            }
        );

        $controllers->post(
            '/current',
            function (Request $request, Application $app) {
                /* @var \CultuurNet\UiTPASBeheer\Counter\CounterService $counterService */
                $counterService = $app['uitpas_counter_service'];

                $counterId = $request->request->get('counterId');
                $counterService->setActiveCounterId($counterId);

                $counter = $counterService->getActiveCounter();
                return new JsonResponse($counter);
            }
        );

        $controllers->get(
            '/current',
            function (Request $request, Application $app) {
                /* @var \CultuurNet\UiTPASBeheer\Counter\CounterService $counterService */
                $counterService = $app['uitpas_counter_service'];

                $counter = $counterService->getActiveCounter();
                return new JsonResponse($counter);
            }
        );

        return $controllers;
    }
}
