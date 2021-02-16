<?php

namespace CultuurNet\UiTPASBeheer\GroupPass;

use CultureFeed_Uitpas_Default;
use ICultureFeed;
use Symfony\Component\HttpFoundation\JsonResponse;

final class GroupPassController
{
    /**
     * @var ICultureFeed
     */
    private $cultureFeedUitpas;

    public function __construct(CultureFeed_Uitpas_Default $cultureFeedUitpas)
    {
        $this->cultureFeedUitpas = $cultureFeedUitpas;
    }

    public function getGroupPassInfo(string $id): JsonResponse
    {
        $pass = $this->cultureFeedUitpas->getGroupPass($id);
        return new JsonResponse($pass);
    }
}
