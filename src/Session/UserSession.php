<?php

namespace CultuurNet\UiTPASBeheer\Session;

use CultuurNet\UiTIDProvider\Session\UserSession as UiTIDUserSession;

class UserSession extends UiTIDUserSession
{
    /**
     * Name of the session variable that stores the selected counter.
     */
    const COUNTER_VARIABLE = 'counter';

    /**
     * @param string $counterId
     */
    public function setCounterId($counterId)
    {
        $this->set(self::COUNTER_VARIABLE, $counterId);
    }

    /**
     * @return string|null
     */
    public function getCounterId()
    {
        return $this->get(self::COUNTER_VARIABLE);
    }

    public function removeCounterId()
    {
        $this->remove(self::COUNTER_VARIABLE);
    }
}
