<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CounterService
{
    const COUNTER_ID_VARIABLE = 'counter_id';

    /**
     * @var \CultureFeed_Uitpas
     */
    protected $uitpas;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var \CultureFeed_User
     */
    protected $user;

    /**
     * @param SessionInterface $session
     * @param \ICultureFeed $cultureFeed
     * @param \CultureFeed_User $user
     */
    public function __construct(SessionInterface $session, \CultureFeed_Uitpas $uitpas, \CultureFeed_User $user)
    {
        $this->uitpas = $uitpas;
        $this->session = $session;
        $this->user = $user;
    }

    /**
     * @return \CultureFeed_Uitpas_Counter_Employee[]
     */
    public function getCounters()
    {
        $unKeyedCounters = $this->uitpas->searchCountersForMember($this->user->id);
        $counters = array();

        foreach ($unKeyedCounters->objects as $counter) {
            $counters[$counter->id] = $counter;
        }

        return $counters;
    }

    /**
     * @param string $id
     *
     * @return \CultureFeed_Uitpas_Counter_Employee|null
     */
    public function getCounter($id)
    {
        $counters = $this->getCounters($this->user);

        if (isset($counters[$id])) {
            return $counters[$id];
        } else {
            return null;
        }
    }

    /**
     * @param string $id
     *
     * @throws CounterNotFoundException
     *   If the provided counter can not be found or is not available for the current user.
     */
    public function setActiveCounterId($id)
    {
        $id = (string) $id;

        if (is_null($this->getCounter($id))) {
            throw new CounterNotFoundException($id, Response::HTTP_BAD_REQUEST);
        }

        $this->session->set(self::COUNTER_ID_VARIABLE, $id);
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
     *
     * @throws CounterNotSetException
     *   If no active counter is set. Use getActiveCounterId() and
     *   getCounter() if you want to get null instead.
     */
    public function getActiveCounter()
    {
        $id = $this->getActiveCounterId();

        if (!is_null($id)) {
            $counter = $this->getCounter($id);
        }

        if (empty($counter)) {
            throw new CounterNotSetException($this->user);
        }

        return $counter;
    }
}
