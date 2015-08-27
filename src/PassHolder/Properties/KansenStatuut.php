<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\DateTime\Date;

final class KansenStatuut
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
        $c = clone $this;
        $c->remarks = $remarks;
        return $c;
    }
}
