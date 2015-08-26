<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use Symfony\Component\HttpFoundation\Request;

class UiTPASController
{
    /**
     * @var UiTPASServiceInterface
     */
    protected $uitpasService;

    /**
     * @param UiTPASServiceInterface $uitpasService
     */
    public function __construct(UiTPASServiceInterface $uitpasService)
    {
        $this->uitpasService = $uitpasService;
    }

    /**
     * @param Request $request
     * @param UiTPASNumber $uitpasNumber
     *
     * @return Price
     */
    public function getPrice(Request $request, UiTPASNumber $uitpasNumber)
    {
    }
}
