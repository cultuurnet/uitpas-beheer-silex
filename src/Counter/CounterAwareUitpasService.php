<?php

namespace CultuurNet\UiTPASBeheer\Counter;

abstract class CounterAwareUitpasService
{
    /**
     * @var \CultureFeed_Uitpas
     */
    private $uitpasService;

    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     */
    public function __construct(\CultureFeed_Uitpas $uitpasService, CounterConsumerKey $counterConsumerKey)
    {
        $this->uitpasService = $uitpasService;
        $this->counterConsumerKey = $counterConsumerKey;
    }

    /**
     * @return \CultureFeed_Uitpas
     */
    protected function getUitpasService()
    {
        return $this->uitpasService;
    }

    /**
     * @var CounterConsumerKey
     */
    protected function getCounterConsumerKeyObject()
    {
        return $this->counterConsumerKey;
    }

    /**
     * @var string
     */
    protected function getCounterConsumerKey()
    {
        return $this->counterConsumerKey->toNative();
    }
}
