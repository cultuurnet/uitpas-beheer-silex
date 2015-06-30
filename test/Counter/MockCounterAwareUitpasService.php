<?php

namespace CultuurNet\UiTPASBeheer\Counter;

class MockCounterAwareUitpasService extends CounterAwareUitpasService
{
    /**
     * @return \CultureFeed_Uitpas
     */
    public function exposeUitpasService()
    {
        return $this->getUitpasService();
    }

    /**
     * @return CounterConsumerKey
     */
    public function exposeCounterConsumerKeyObject()
    {
        return $this->getCounterConsumerKeyObject();
    }

    /**
     * @return string
     */
    public function exposeCounterConsumerKey()
    {
        return $this->getCounterConsumerKey();
    }
}
