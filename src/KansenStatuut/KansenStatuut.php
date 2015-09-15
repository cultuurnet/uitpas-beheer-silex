<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use ValueObjects\DateTime\Date;

/**
 * @todo Move remarks property to PassHolder.
 * @see http://jira.uitdatabank.be:8080/browse/UBR-235
 */
final class KansenStatuut
{
    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var Remarks|null
     */
    protected $remarks;

    /**
     * @var KansenStatuutStatus|null
     */
    protected $status;

    /**
     * @var UiTPAS|null
     */
    protected $uitPas;

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

    /**
     * @param Remarks $remarks
     * @return KansenStatuut
     */
    public function withRemarks(Remarks $remarks)
    {
        $c = clone $this;
        $c->remarks = $remarks;
        return $c;
    }

    /**
     * @return KansenStatuutStatus|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param KansenStatuutStatus $status
     * @return KansenStatuut
     */
    public function withStatus(KansenStatuutStatus $status)
    {
        $c = clone $this;
        $c->status = $status;
        return $c;
    }

    /**
     * @return UiTPAS|null
     */
    public function getUiTPAS()
    {
        return $this->uitPas;
    }

    /**
     * @param UiTPAS $uitPas
     * @return KansenStatuut
     */
    public function withUiTPAS(UiTPAS $uitPas)
    {
        $c = clone $this;
        $c->uitPas = $uitPas;
        return $c;
    }
}
