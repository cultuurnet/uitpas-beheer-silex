<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\Properties\DateRange;

interface ExpenseReportServiceInterface
{
    /**
     * @return DateRange[]
     */
    public function getPeriods();

    /**
     * @param DateRange $dateRange
     * @return ExpenseReportInfo
     */
    public function generate(DateRange $dateRange);

    /**
     * @param ExpenseReportId $id
     * @return ExpenseReportStatus
     */
    public function getStatus(ExpenseReportId $id);
}
