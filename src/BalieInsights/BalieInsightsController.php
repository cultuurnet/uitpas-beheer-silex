<?php
/**
 * @file
 * Controller for balie insights.
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BalieInsightsController
{
  /**
   * @var BalieInsightsServiceInterface
   */
  private $balieInsightsService;

  public function __construct(BalieInsightsServiceInterface $balieInsightsService)
  {
    $this->balieInsightsService = $balieInsightsService;
  }

  /**
   * @return JsonResponse
   */
  public function getCardSales(Request $request)
  {
    $response = new JsonResponse();
    $response->setContent($this->balieInsightsService->getCardSales($request->query));

    return $response;
  }

  /**
   * @return JsonResponse
   */
  public function getExchanges(Request $request)
  {
    $response = new JsonResponse();
    $response->setContent($this->balieInsightsService->getExchanges($request->query));

    return $response;
  }

  /**
   * @return JsonResponse
   */
  public function getMias(Request $request)
  {
    $response = new JsonResponse();
    $response->setContent($this->balieInsightsService->getMias($request->query));

    return $response;
  }

  /**
   * @return JsonResponse
   */
  public function getCheckins(Request $request)
  {
    $response = new JsonResponse();
    $response->setContent($this->balieInsightsService->getCheckins($request->query));

    return $response;
  }
}
