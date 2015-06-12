<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class CounterService
{
    const COUNTER_ID_VARIABLE = 'counter_id';

    /**
     * @var \CultureFeed
     */
    protected $cultureFeed;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \CultureFeed_User
     */
    protected $user;

    /**
     * @param \CultureFeed $cultureFeed
     */
    public function __construct(Session $session, \CultureFeed $cultureFeed, \CultureFeed $user)
    {
        $this->cultureFeed = $cultureFeed;
        $this->session = $session;
        $this->user = $user;
    }

    /**
     * @param \CultureFeed_User $user
     *   Optionally the user for which to get the counters.
     *   If not provided this will default to the current user.
     *
     * @return \CultureFeed_Uitpas_Counter_Employee[]
     */
    public function getCounters(\CultureFeed_User $user = null)
    {
        if (is_null($user)) {
            $user = $this->user;
        }

        $unKeyedCounters = $this->cultureFeed
            ->uitpas()
            ->searchCountersForMember($user->id);
        $counters = array();

        foreach ($unKeyedCounters->objects as $counter) {
            $counters[$counter->id] = $counter;
        }

        return $counters;
    }

    /**
     * @param string $id
     * @param \CultureFeed_User $user
     *   Optionally the user for which to get the counter.
     *   If not provided this will default to the current user.
     *
     * @return \CultureFeed_Uitpas_Counter_Employee|null
     */
    public function getCounter($id, \CultureFeed_User $user = null)
    {
        if (is_null($user)) {
            $user = $this->user;
        }

        $counters = $this->getCounters($user);

        if (isset($counters[$id])) {
            return $counters[$id];
        } else {
            return null;
        }
    }

    /**
     * @param string $id
     */
    public function setActiveCounterId($id)
    {
        $id = (string) $id;

        if (!is_null($this->getCounter($id))) {
            $this->session->set(self::COUNTER_ID_VARIABLE, $id);
        } else {
            throw new CounterNotFoundException($id);
        }
    }

    /**
     * @return string
     */
    public function getActiveCounterId()
    {
        return $this->session->get(self::COUNTER_ID_VARIABLE);
    }

    /**
     * @return \CultureFeed_Uitpas_Counter_Employee
     */
    public function getActiveCounter()
    {
        $id = $this->session->get(self::COUNTER_ID_VARIABLE);

        if (is_null($id)) {
            throw new CounterNotSetException($this->user);
        }

        $counter = $this->getCounter($id);

        if (!is_null($counter)) {
            return $counter;
        } else {
            throw new CounterNotFoundException($id);
        }
    }
}
