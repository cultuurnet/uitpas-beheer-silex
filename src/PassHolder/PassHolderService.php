<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

class PassHolderService implements PassHolderServiceInterface
{
    /**
     * @var \CultureFeed_Uitpas
     */
    protected $uitpasService;

    /**
     * @var string|null
     */
    protected $counterConsumerKey = null;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param string|null $counterConsumerKey
     */
    public function __construct(\CultureFeed_Uitpas $uitpasService, $counterConsumerKey = null)
    {
        $this->uitpasService = $uitpasService;
        $this->counterConsumerKey = $counterConsumerKey;
    }

    /**
     * @param $identification
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByIdentificationNumber($identification)
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
