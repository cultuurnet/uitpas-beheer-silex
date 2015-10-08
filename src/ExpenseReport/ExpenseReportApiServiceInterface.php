<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;

interface ExpenseReportApiServiceInterface
{
    /**
     * @param ExpenseReportId $id
     * @return ExpenseReportDownload
     */
    public function download(ExpenseReportId $id);
}
