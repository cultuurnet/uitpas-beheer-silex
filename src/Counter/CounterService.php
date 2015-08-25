<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CounterService implements CounterServiceInterface
{
    const COUNTER_SESSION_VARIABLE = 'counter';

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
     * @param \CultureFeed_Uitpas $uitpas
     * @param \CultureFeed_User $user
     */
    public function __construct(SessionInterface $session, \CultureFeed_Uitpas $uitpas, \CultureFeed_User $user)
    {
        $this->uitpas = $uitpas;
        $this->session = $session;
        $this->user = $user;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getCounter($id)
    {
        $counters = $this->getCounters();

        if (isset($counters[$id])) {
            return $counters[$id];
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function setActiveCounter(\CultureFeed_Uitpas_Counter_Employee $counter)
    {
        $this->session->set(self::COUNTER_SESSION_VARIABLE, $counter);
    }

    /**
     * @inheritdoc
     */
    public function getActiveCounter()
    {
        $counter = $this->session->get(self::COUNTER_SESSION_VARIABLE);

        if (is_null($counter)) {
            throw new CounterNotSetException();
        }

        return $counter;
    }
}
