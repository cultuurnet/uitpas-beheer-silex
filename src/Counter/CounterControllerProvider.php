<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use CultuurNet\UiTPASBeheer\Response\JsonErrorResponse;
use CultuurNet\UiTPASBeheer\Response\JsonSuccessResponse;
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

                try {
                    $counterId = $request->request->get('counterId');
                    $counterService->setActiveCounterId($counterId);
                    $message = sprintf('Active counter was set to id %s.', $counterId);
                    return new JsonSuccessResponse($message);
                } catch(ResponseException $exception) {
                    return new JsonErrorResponse($exception);
                }
            }
        );

        $controllers->get(
            '/current',
            function (Request $request, Application $app) {
                /* @var \CultuurNet\UiTPASBeheer\Counter\CounterService $counterService */
                $counterService = $app['uitpas_counter_service'];

                try {
                    $counter = $counterService->getActiveCounter();
                    return new JsonResponse($counter);
                } catch (ResponseException $exception) {
                    return new JsonErrorResponse($exception);
                }
            }
        );

        return $controllers;
    }
}
