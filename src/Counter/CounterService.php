<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CounterService implements CounterServiceInterface
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
    public function setActiveCounterId($id)
    {
        $id = (string) $id;

        if (is_null($this->getCounter($id))) {
            throw new CounterNotFoundException($id, Response::HTTP_BAD_REQUEST);
        }

        $this->session->set(self::COUNTER_ID_VARIABLE, $id);
    }

    /**
     * @inheritdoc
     */
    public function getActiveCounterId()
    {
        return $this->session->get(self::COUNTER_ID_VARIABLE);
    }

    /**
     * @inheritdoc
     */
    public function getActiveCounter()
    {
        $id = $this->getActiveCounterId();

        if (!is_null($id)) {
            $counter = $this->getCounter($id);
        }

        if (empty($counter)) {
            throw new CounterNotSetException();
        }

        return $counter;
    }
}
