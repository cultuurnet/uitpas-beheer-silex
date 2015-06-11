<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CounterControllerProvider implements ControllerProviderInterface {

  /**
   * Returns routes to connect to the given application.
   *
   * @param Application $app An Application instance
   *
   * @return ControllerCollection A ControllerCollection instance
   */
  public function connect(Application $app) {
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
        /* @var \CultureFeed_User $user */
        $user = $app['uitid_current_user'];

        /* @var \CultureFeed $cultureFeed */
        $cultureFeed = $app['culturefeed'];

        $counters = $cultureFeed->uitpas()->searchCountersForMember($user->id);

        return new JsonResponse($counters);
      }
    );

    return $controllers;
  }
}
