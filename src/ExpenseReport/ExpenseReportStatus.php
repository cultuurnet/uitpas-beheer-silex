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

    /**
     * @param bool $completed
     * @param Url|null $downloadUrl
     */
    public function __construct($completed, Url $downloadUrl = null)
    {
        if ($completed && is_null($downloadUrl)) {
            throw new \InvalidArgumentException(
                'downloadUrl should not be null if expense report generation is completed.'
            );
        }

        if (!$completed && !is_null($downloadUrl)) {
            throw new \InvalidArgumentException(
                'downloadUrl should be null if expense report generation is not completed.'
            );
        }

        $this->completed = (bool) $completed;
        $this->downloadUrl = $downloadUrl;
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
