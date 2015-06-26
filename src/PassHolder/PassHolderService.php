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

    /**
     * @param string $uitpasNumber
     *
     * @return \CultureFeed_Uitpas_Passholder|null
     */
    public function getByUitpasNumber($uitpasNumber)
    {
        try {
            return $this->uitpasService->getPassholderByUitpasNumber(
                $uitpasNumber,
                $this->counterConsumerKey
            );
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     */
    public function update(\CultureFeed_Uitpas_Passholder $passHolder)
    {
        try {
            $this->uitpasService->updatePassholder($passHolder);
        } catch (\CultureFeed_Exception $exception) {
            throw new PassHolderUpdateException(400, $exception);
        }
    }
}
