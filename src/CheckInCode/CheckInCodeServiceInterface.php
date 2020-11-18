<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\UiTPASBeheer\CheckInCode\CheckInCodeDownload;
use ValueObjects\StringLiteral\StringLiteral;

interface CheckInCodeServiceInterface
{
    /**
     * @param StringLiteral $activityId
     * @param bool $zipped
     * @return CheckInCodeDownload
     */
    public function download(StringLiteral $activityId, $zipped = false);
}
