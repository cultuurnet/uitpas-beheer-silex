<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\Number\Integer;

class PagedResultSet
{
    /**
     * @var Activity[]
     */
    protected $activities;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $total;

    /**
     * @param Integer $total
     * @param Activity[] $activities
     * @throws \LogicException
     */
    public function __construct(Integer $total, array $activities)
    {
        $this->guardLogicTotal($total, $activities);

        $this->total = $total;
        $this->activities = $activities;
    }

    /**
     * @return Activity[]
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @return \ValueObjects\Number\Integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    private function guardLogicTotal(Integer $total, $activities)
    {
        if (count($activities) > $total->toNative()) {
            throw new \LogicException(
                'Total can not be lower than the amount of current results'
            );
        }
    }
}
