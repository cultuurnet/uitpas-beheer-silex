<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\Properties\DateRange;

class ExpenseReportInfo implements \JsonSerializable
{
    /**
     * @var ExpenseReportId
     */
    private $id;

    /**
     * @var DateRange
     */
    private $dateRange;

    /**
     * @param ExpenseReportId $id
     * @param DateRange $dateRange
     */
    public function __construct(ExpenseReportId $id, DateRange $dateRange)
    {
        $this->id = $id;
        $this->dateRange = $dateRange;
    }

    /**
     * @return ExpenseReportId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateRange
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->toNative(),
            'range' => $this->dateRange->jsonSerialize(),
        ];
    }
}
