<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterService;

class PassHolderService
{
    /**
     * @var \CultureFeed_Uitpas
     */
    protected $uitpasService;

    /**
     * @var CounterService
     */
    protected $counterService;

    /**
     * @var string|null
     */
    protected $counterConsumerKey = null;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterService $counterService
     */
    public function __construct(\CultureFeed_Uitpas $uitpasService, CounterService $counterService)
    {
        $this->uitpasService = $uitpasService;
        $this->counterService = $counterService;

        $counterId = $this->counterService->getActiveCounterId();
        $counter = $this->counterService->getCounter($counterId);
        if ($counter) {
            $this->counterConsumerKey = $counter->consumerKey;
        }
    }

    /**
     * @param $identification
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getPassHolderByIdentificationNumber($identification)
    {
        try {
            return $this->uitpasService->getPassholderByIdentificationNumber(
                $identification,
                $this->counterConsumerKey
            );
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }
}
