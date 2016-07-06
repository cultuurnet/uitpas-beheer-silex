<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Symfony\Component\HttpFoundation\ParameterBag;

interface BalieInsightsServiceInterface {
  public function getCardSales(ParameterBag $query);
  public function getExchanges(ParameterBag $query);
  public function getMias(ParameterBag $query);
  public function getCheckins(ParameterBag $query);
}