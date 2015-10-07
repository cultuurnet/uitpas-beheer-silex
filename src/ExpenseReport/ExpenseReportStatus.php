<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use ValueObjects\Web\Url;

final class ExpenseReportStatus implements \JsonSerializable
{
    /**
     * @var bool
     */
    private $complete;

    /**
     * @var Url|null
     */
    private $downloadUrl;


    private function __construct()
    {

    }

    /**
     * @return ExpenseReportStatus
     */
    public static function incomplete()
    {
        $status = new self();
        $status->complete = false;
        return $status;
    }

    /**
     * @param Url $downloadUrl
     * @return ExpenseReportStatus
     */
    public static function complete(Url $downloadUrl)
    {
        $status = new self();
        $status->complete = true;
        $status->downloadUrl = $downloadUrl;
        return $status;
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->complete;
    }

    /**
     * @return Url|null
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data['completed'] = $this->complete;

        if (!is_null($this->downloadUrl)) {
            $data['download'] = (string) $this->downloadUrl;
        }

        return $data;
    }
}
