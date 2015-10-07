<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use ValueObjects\Web\Url;

final class ExpenseReportStatus implements \JsonSerializable
{
    /**
     * @var bool
     */
    private $completed;

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
    public static function inProgress()
    {
        $status = new self();
        $status->completed = false;
        return $status;
    }

    /**
     * @param Url $downloadUrl
     * @return ExpenseReportStatus
     */
    public static function completed(Url $downloadUrl)
    {
        $status = new self();
        $status->completed = true;
        $status->downloadUrl = $downloadUrl;
        return $status;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed;
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
        $data['completed'] = $this->completed;

        if (!is_null($this->downloadUrl)) {
            $data['download'] = (string) $this->downloadUrl;
        }

        return $data;
    }
}
