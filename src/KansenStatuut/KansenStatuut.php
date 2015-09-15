<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use ValueObjects\DateTime\Date;

/**
 * @todo Move remarks property to PassHolder.
 * @see http://jira.uitdatabank.be:8080/browse/UBR-235
 */
final class KansenStatuut implements \JsonSerializable
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
     * @var CardSystem|null
     */
    protected $cardSystem;

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
     * @return CardSystem|null
     */
    public function getCardSystem()
    {
        return $this->cardSystem;
    }

    /**
     * @param CardSystem $cardSystem
     * @return KansenStatuut
     */
    public function withUiTPAS(CardSystem $cardSystem)
    {
        $c = clone $this;
        $c->cardSystem = $cardSystem;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'endDate' => $this->endDate->toNativeDateTime()->format('Y-m-d'),
        ];

        if (!is_null($this->status)) {
            $data['status'] = $this->status->toNative();
        }

        if (!is_null($this->cardSystem)) {
            $data['cardSystem'] = $this->cardSystem->jsonSerialize();
        }

        return $data;
    }
}
