<?php

namespace CultuurNet\UiTPASBeheer\Counter;

interface CounterServiceInterface
{
    /**
     * @return \CultureFeed_Uitpas_Counter_Employee[]
     */
    public function getCounters();

    /**
     * @param string $id
     *
     * @return \CultureFeed_Uitpas_Counter_Employee|null
     */
    public function getCounter($id);

    /**
     * @param string $id
     *
     * @throws CounterNotFoundException
     *   If the provided counter can not be found or is not available for the current user.
     */
    public function setActiveCounterId($id);

    /**
     * @return string
     */
    public function getActiveCounterId();

    /**
     * @return \CultureFeed_Uitpas_Counter_Employee
     *
     * @throws CounterNotSetException
     *   If no active counter is set. Use getActiveCounterId() and
     *   getCounter() if you want to get null instead.
     */
    public function getActiveCounter();
}
