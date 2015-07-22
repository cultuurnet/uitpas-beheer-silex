<?php

namespace CultuurNet\UiTPASBeheer\ResultSet;

interface PagedResultSetInterface
{
    /**
     * @return array
     */
    public function getResults();

    /**
     * @return \ValueObjects\Number\Integer
     */
    public function getTotal();
}
