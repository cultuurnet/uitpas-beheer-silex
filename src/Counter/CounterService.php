<?php

namespace CultuurNet\UiTPASBeheer\Counter;

class CounterService
{

    /**
     * @var \CultureFeed
     */
    protected $cultureFeed;

    /**
     * @param \CultureFeed $cultureFeed
     */
    public function __construct(\CultureFeed $cultureFeed)
    {
        $this->cultureFeed = $cultureFeed;
    }

    /**
     * @param \CultureFeed_User $user
     * @return array
     */
    public function getCounters(\CultureFeed_User $user)
    {
        $unKeyedCounters = $this->cultureFeed->uitpas()
            ->searchCountersForMember($user->id);
        $counters = array();

        foreach ($unKeyedCounters->objects as $counter) {
            $counters[$counter->id] = $counter;
        }

        return $counters;
    }

    public function validateUserHasCounter()
    {

    }

    public function setActiveCounter()
    {

    }

    public function getActiveCounter()
    {

    }
}
