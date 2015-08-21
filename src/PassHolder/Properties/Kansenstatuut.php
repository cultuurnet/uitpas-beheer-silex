<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\DateTime\Date;

final class Kansenstatuut
{
    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var Remarks
     */
    protected $remarks;

    /**
     * @param Date $endDate
     */
    public function __construct(Date $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return Date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return Remarks|null
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    public function withRemarks(Remarks $remarks)
    {
        $kansenstatuut = clone $this;
        $kansenstatuut->remarks = $remarks;

        return $kansenstatuut;
    }
}
