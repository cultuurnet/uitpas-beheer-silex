<?php
/**
 * @file
 * Provides controllers for balie insights.
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class BalieInsightsControllerProvider implements ControllerProviderInterface
{
  public function connect(Application $app)
  {
      $app['balie_insights_controller'] = $app->share(
          function (Application $app) {
              return new BalieInsightsController(
                $app['balie_insights_service']
              );
          }
      );

    /* @var ControllerCollection $controllers */
    $controllers = $app['controllers_factory'];
    $controllers->get('counters/cardsales', 'balie_insights_controller:getCardSales');
    $controllers->get('counters/exchanges', 'balie_insights_controller:getExchanges');
    $controllers->get('counters/mias', 'balie_insights_controller:getMias');
    $controllers->get('counters/checkins', 'balie_insights_controller:getCheckins');

    return $controllers;
  }
}
